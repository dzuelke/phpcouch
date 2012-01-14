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

class CurlAdapter implements AdapterInterface {
	
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
	public function sendRequest(HttpRequest $request) {
		$options = $this->options;
		
		if(null !== ($payload = $request->getContent())) {
			$options['content'] = $payload;
			$options['header'][] = 'Content-Length: ' . strlen($payload);
		}
		
		// additional headers
		foreach($request->getHeaders() as $key => $values) {
			foreach($values as $value) {
				$options['header'][] = "$key: $value";
			}
		}
		
		$curl = curl_init($request->getDestination());
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
		curl_setopt($curl, CURLOPT_HTTPHEADER, $options['header']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getContent());
		curl_setopt($curl, CURLOPT_USERAGENT, $options['user_agent']);
		
		foreach($options['curl'] as $cUrlOption => $value) {
			curl_setopt($curl, $cUrlOption, $value);
		}
		
		$body     = curl_exec($curl);
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