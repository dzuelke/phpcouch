<?php

namespace phpcouch\adapter;

use phpcouch\Exception;

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
	public function sendRequest($method, $url, array $headers = array(), $payload = null)
	{
		$options = $this->options;
		
		$options['http']['method'] = $method;
		
		$options['http']['header'] = array();
		
		if($payload !== null) {
			$options['http']['content'] = $payload;
			$options['http']['header'][] = 'Content-Length: ' . strlen($payload);
		}
		
		// build our list of additional headers from the method argument
		array_walk($headers, function($value, $key) use($options) { $options['http']['header'][] = "$key: $value"; });
		
		$ctx = stream_context_create($options);
		
		$fp = @fopen($url, 'r', false, $ctx);
		
		if($fp === false) {
			$error = error_get_last();
			throw new Exception($error['message']);
		}
		
		$meta = stream_get_meta_data($fp);
		
		// $meta['wrapper_data'] is an indexed array of the individual response header lines
		
		if(
			!isset($meta['wrapper_data'][0]) ||
			!($status = preg_match('#^HTTP/1\.[01]\s+(\d{3})\s+(.+)$#', $meta['wrapper_data'][0], $matches))
		) {
			throw new Exception('Could not read HTTP response status line');
		}
		
		$statusCode = (int)$matches[1];
		$statusMessage = $matches[2];
		
		$body = stream_get_contents($fp);
		
		if($statusCode >= 400) {
			if($statusCode % 500 < 100) {
				// a 5xx response
				throw new Exception($statusMessage, $statusCode);
				// throw new Exception($statusMessage, $statusCode, json_decode($body));
			} else {
				// a 4xx response
				throw new Exception($statusMessage, $statusCode);
				// throw new Exception($statusMessage, $statusCode, json_decode($body));
			}
		} else {
			return array($body, $meta['wrapper_data']);
		}
	}
}

?>
