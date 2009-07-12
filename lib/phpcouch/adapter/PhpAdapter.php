<?php

namespace phpcouch\adapter;

use phpcouch\Exception;
use phpcouch\http\HttpResponse, phpcouch\http\HttpClientErrorException, phpcouch\http\HttpServerErrorException;

/**
 * An adapter implemented using the PHP >= 5.3 HTTP fopen wrapper.
 *
 * @package    PHPCouch
 * @subpackage Adapter
 *
 * @author     David Zülke <david.zuelke@bitextender.com>
 * @copyright  Bitextender GmbH
 *
 * @since      1.0.0
 *
 * @version    $Id$
 */
class PhpAdapter implements AdapterInterface
{
	protected $options = array();
	
	/**
	 * Adapter constructor.
	 *
	 * @param      array An array of initialization options for this driver implementation.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __construct(array $options = array())
	{
		$this->options = array(
			'http' => array(
				'header' => array(
					'Accept: */*, application/json',
				),
				'ignore_errors' => true,
				'user_agent' => \phpcouch\Phpcouch::getVersionString(),
			),
		);
		
		// can't do array_merge_recursive
		foreach($options as $wrapper => $opts) {
			if(isset($this->options[$wrapper])) {
				$this->options[$wrapper] = array_merge($this->options[$wrapper], $opts);
			} else {
				$this->options[$wrapper] = $opts;
			}
		}
	}
	
	/**
	 * Perform the HTTP request.
	 *
	 * @param      string The HTTP method to use.
	 * @param      string The URL to call.
	 * @param      array  Optional HTTP headers.
	 * @param      string Optional request body payload.
	 *
	 * @return     array  The response from the server as an indexed array of a content string and a headers array.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 * @since      1.0.0
	 */
	public function sendRequest(\phpcouch\http\HttpRequest $request)
	{
		$options = $this->options;
		$options['http']['method'] = $request->getMethod();
		
		if(null !== ($payload = $request->getContent())) {
			$options['http']['content'] = $payload;
			$options['http']['header'][] = 'Content-Length: ' . strlen($payload);
		}
		
		// additional headers
		foreach($request->getHeaders() as $key => $values) {
			foreach($values as $value) {
				$options['http']['header'][] = "$key: $value";
			}
		}
		
		// must do this as there is a bug in all current PHP versions causing an additional \r\n being appended to the last header (so there are two \r\n sequences before the request body starts) when it's an array
		// a string it is then, that fixes the problem
		$options['http']['header'] = implode("\r\n", $options['http']['header']);
		
		$ctx = stream_context_create($options);
		
		$fp = @fopen($request->getDestination(), 'r', false, $ctx);
		
		if($fp === false) {
			$error = error_get_last();
			throw new TransportException($error['message']);
		}
		
		$meta = stream_get_meta_data($fp);
		
		// $meta['wrapper_data'] is an indexed array of the individual response header lines
		// when a redirect happens, we get all the response headers merged together
		// so we need to run in a loop and create a chain of responses until we reach the final, authoritative one
		// [0]=>
		// string(30) "HTTP/1.0 301 Moved Permanently"
		// [1]=>
		// string(39) "Server: CouchDB/0.9.0 (Erlang OTP/R13B)"
		// [2]=>
		// string(73) "Location: http://localhost:5984/test_suite_db%2Fwith_slashes/_design/test"
		// [3]=>
		// string(35) "Date: Sat, 11 Jul 2009 21:33:46 GMT"
		// [4]=>
		// string(17) "Content-Length: 0"
		// [5]=>
		// string(15) "HTTP/1.0 200 OK"
		// [6]=>
		// string(39) "Server: CouchDB/0.9.0 (Erlang OTP/R13B)"
		// [7]=>
		// string(19) "Etag: "1-573696572""
		// [8]=>
		// string(35) "Date: Sat, 11 Jul 2009 21:33:46 GMT"
		// [9]=>
		// string(30) "Content-Type: application/json"
		// [10]=>
		// string(18) "Content-Length: 96"
		// [11]=>
		// string(30) "Cache-Control: must-revalidate"
		
		foreach($meta['wrapper_data'] as $headerLine) {
			if(preg_match('#^HTTP/1\.[01]\s+(\d{3})\s+(.+)$#', $headerLine, $matches)) {
				$statusCode = (int)$matches[1];
				$statusMessage = $matches[2];
				
				if(isset($response)) {
					$response = new HttpResponse($response);
				} else {
					$response = new HttpResponse();
				}
				$response->setStatusCode($statusCode);
				
				continue;
			}
			
			if(!isset($response)) {
				throw new TransportException('Could not read HTTP response status line');
			}
			
			$headerParts = explode(':', $headerLine, 2);
			if(count($headerParts) == 2) {
				$response->setHeader(trim($headerParts[0]), trim($headerParts[1]));
			}
		}
		
		$body = stream_get_contents($fp);
		$response->setContent($body);
		
		if($statusCode >= 400) {
			if($statusCode % 500 < 100) {
				// a 5xx response
				throw new HttpServerErrorException($statusMessage, $statusCode, $response);
			} else {
				// a 4xx response
				throw new HttpClientErrorException($statusMessage, $statusCode, $response);
			}
		} else {
			return $response;
		}
	}
}

?>