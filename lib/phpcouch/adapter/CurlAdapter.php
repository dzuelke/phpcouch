<?php
namespace phpcouch\adapter;

use \phpcouch\http\HttpRequest;
use \phpcouch\http\HttpResponse;
use \phpcouch\http\HttpClientErrorException; 
use \phpcouch\http\HttpServerErrorException;

/**
 * An adapter implemented using cURL
 *
 * @package    PHPCouch
 * @subpackage Adapter
 *
 * @author     Peter Limbach <peter.limbach@gmail.com>
 * @copyright  Unitedprototype GmbH
 *
 * @since      1.0.0
 *
 * @version    $Id$
 */

class CurlAdapter implements AdapterInterface
{
	
	/**
	 * Constructor
	 *
	 * @param      array adapter options
	 *
	 * @author     Peter Limbach <peter.limbach@gmail.com>
	 */
	public function __construct(array $options = array()) {
		$this->options =  array(
			'header' => array(
				'Accept: */*, application/json',
			),
			'ignore_errors' => true,
			'user_agent' => \phpcouch\Phpcouch::getVersionString(),
			'curl' => array(CURLOPT_RETURNTRANSFER => true),
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
	 * Perform the HTTP request
	 *
	 * @param      \phpcouch\http\HttpRequest  HTTP request object
	 *
	 * @return     \phpcouch\http\HttpResponse The response from the server
	 *
	 * @throws     TransportException
	 *
	 * @author     Peter Limbach <peter.limbach@gmail.com>
	 */
	public function sendRequest(HttpRequest $request) {
		$options = $this->options;
		
		$content = $request->getContent();
		if($content !== null) {
			if(is_resource($content)) {
				$stat = fstat($content);
				$length = $stat['size'];
			} else {
				$length = strlen($content);
			}
			
			$options['header'][] = 'Content-Length: ' . $length;
		}
		
		// additional headers
		foreach($request->getHeaders() as $key => $values) {
			foreach($values as $value) {
				$options['header'][] = "$key: $value";
			}
		}
		
		$curl = curl_init($request->getDestination());
		if(is_resource($content) && $request->getMethod() == HttpRequest::METHOD_PUT) {
			// cURL needs this in combination with CURLOPT_INFILE
			curl_setopt($curl, CURLOPT_PUT, true);
		} else {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
		}
		curl_setopt($curl, CURLOPT_HTTPHEADER, $options['header']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if(is_resource($content)) {
			rewind($content);
			curl_setopt($curl, CURLOPT_INFILE, $content);
			curl_setopt($curl, CURLOPT_INFILESIZE, $length);
		} else {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		}
		curl_setopt($curl, CURLOPT_USERAGENT, $options['user_agent']);
		
		foreach($options['curl'] as $cUrlOption => $value) {
			curl_setopt($curl, $cUrlOption, $value);
		}
		
		$body = curl_exec($curl);
		
		if($body === false) {
			throw new TransportException(curl_error($curl));
		}
		
		$response = new HttpResponse();
		$response->setContent($body);
		
		$info = curl_getinfo($curl);
		
		$response->setContentType($info['content_type']);
		$response->setStatusCode($info['http_code']);
		
		curl_close($curl);
		
		if($info['http_code'] >= 400) {
			if($info['http_code'] % 500 < 100) {
				// a 5xx response
				throw new HttpServerErrorException($response->getStatusMessage(), $info['http_code'], $response);
			} else {
				// a 4xx response
				throw new HttpClientErrorException($response->getStatusMessage(), $info['http_code'], $response);
			}
		} else {
			return $response;
		}
	}
}

?>