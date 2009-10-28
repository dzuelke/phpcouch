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
 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
 * @copyright  Bitextender GmbH
 *
 * @since      1.0.0
 *
 * @version    $Id$
 */
class HttpAdapter implements AdapterInterface
{
	protected static $httpMethods = array(
		'GET'     => HTTP_METH_GET,
		'PUT'     => HTTP_METH_PUT,
		'DELETE'  => HTTP_METH_DELETE,
		'POST'    => HTTP_METH_POST,
		'HEAD'    => HTTP_METH_HEAD
	);
	
	protected $options = array();
	
	protected $headers = array();
	
	/**
	 * Adapter constructor.
	 *
	 * @param      array An array of initialization options for this driver implementation.
	 *
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 * @since      1.0.0
	 */
	public function __construct(array $options = array())
	{
		$this->headers = array(
			'Accept' => '*/*, application/json',
			'User-Agent' => \phpcouch\Phpcouch::getVersionString(),
		);
	}
	
	/**
	 * Perform the HTTP request.
	 *
	 * @param      HttpRequest HTTP Request object
	 *
	 * @return     array  The response from the server as an indexed array of a content string and a headers array.
	 *
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 * @since      1.0.0
	 */
	public function sendRequest(\phpcouch\http\HttpRequest $request)
	{
		$internalRequest = new \HttpRequest($request->getDestination(), self::$httpMethods[$request->getMethod()]);
		
		// additional headers
		foreach($request->getHeaders() as $key => $values) {
			foreach($values as $value) {
				$this->headers[$key] = $value;
			}
		}
		
		if (!isset($this->headers['Content-Type'])) {
			$this->headers['Content-Type'] = 'application/json';
		}
		
		if(null !== ($payload = $request->getContent())) {
			if ('PUT' == $request->getMethod()) {
				$internalRequest->setPutData($payload);
			} elseif ('POST' == $request->getMethod()) {
				$internalRequest->setBody($payload);
				$this->headers['Content-Length'] = strlen($payload);
			}
		}
		
		$internalRequest->addHeaders($this->headers);
		
		$message = new \HttpMessage($internalRequest->send());
		
		$response = new HttpResponse();
		
		$response->setStatusCode($message->getResponseCode());
		
		if(!isset($response)) {
			throw new TransportException('Could not read HTTP response status line');
		}
		
		foreach ($message->getHeaders() as $key => $value) {
			$response->setHeader($key, $value);
		}
		
		$response->setContent($message->getBody());
		
		if ($message->getResponseCode() >= 400) {
			if($message->getResponseCode() % 500 < 100) {
				// a 5xx response
				throw new HttpServerErrorException($message->getResponseStatus(), $message->getResponseCode(), $response);
			} else {
				// a 4xx response
				throw new HttpClientErrorException($message->getResponseStatus(), $message->getResponseCode(), $response);
			}
		}
		
		return $response;
	}
}

?>