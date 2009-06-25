<?php

namespace phpcouch\adapter;

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
class Php implements AdapterInterface
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
					'Content-Type: application/json',
					'User-Agent: ' . \phpcouch\Phpcouch::getVersionString(),
				),
				'ignore_errors' => true,
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
	 * Perform the actual request.
	 *
	 * @param      string The URL to call.
	 * @param      string The HTTP method to use.
	 * @param      array  The data to serialize to JSON and send.
	 * @param      array  HTTP headers.
	 *
	 * @return     stdClass The unserialized response JSON.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 * @since      1.0.0
	 */
	protected function doRequest($uri, $method = 'GET', $data = null, $headers = array())
	{
		$options = $this->options;
		$options['http']['method'] = $method;
			
		$sendHeaders = array();
		
		// form valid headers
		foreach($headers as $key => $value) {
			array_push($options['http']['header'], "$key: $value\r\n");
		}

		if($data !== null) {
			// data to send, let's encode it to JSON
			$options['http']['content'] = json_encode($data);
		}
		
		$ctx = stream_context_create($options);
		
		$fp = @fopen($uri, 'r', false, $ctx);
		
		if($fp === false) {
			$error = error_get_last();
			throw new \phpcouch\exception\Adapter($error['message']);
		}
		
		$meta = stream_get_meta_data($fp);
		
		if(
			!isset($meta['wrapper_data'][0]) ||
			!($status = preg_match('#^HTTP/1\.[01]\s+(\d{3})\s+(.+)$#', $meta['wrapper_data'][0], $matches))
		) {
			throw new \phpcouch\exception\Adapter('Could not read HTTP response status line');
		}
		
		$statusCode = (int)$matches[1];
		$statusMessage = $matches[2];
		
		$body = stream_get_contents($fp);
		
		if($statusCode >= 400) {
			if($statusCode % 500 < 100) {
				// a 5xx response
				throw new PhpcouchServerErrorException($statusMessage, $statusCode, json_decode($body));
			} else {
				// a 4xx response
				throw new PhpcouchClientErrorException($statusMessage, $statusCode, json_decode($body));
			}
		} else {
			// finally, decode the JSON body and return it
			return json_decode($body);
		}
	}
	
	/**
	 * Perform an HTTP PUT request.
	 *
	 * @param      string The URL to call.
	 * @param      array  The JSON data to serialize and send.
	 *
	 * @return     stdClass The JSON response.
	 *
	 * @throws     PhpcouchException ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function put($uri, $data = null)
	{
		return $this->doRequest($uri, 'PUT', $data);
	}
	
	/**
	 * Perform an HTTP GET request.
	 *
	 * @param      string The URL to call.
	 *
	 * @return     stdClass The JSON response.
	 *
	 * @throws     PhpcouchException ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function get($uri)
	{
		return $this->doRequest($uri, 'GET');
	}
	
	/**
	 * Perform an HTTP POST request.
	 *
	 * @param      string The URL to call.
	 * @param      array  The JSON data to serialize and send.
	 *
	 * @return     stdClass The JSON response.
	 *
	 * @throws     PhpcouchException ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function post($uri, $data = null)
	{
		return $this->doRequest($uri, 'POST', $data);
	}
	
	/**
	 * Perform an HTTP DELETE request.
	 *
	 * @param      string The URL to call.
	 * @param 	   array  HTTP headers.
	 *
	 * @return     stdClass The JSON response.
	 *
	 * @throws     PhpcouchException ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 * @since      1.0.0
	 */
	public function delete($uri, $headers = array())
	{
		return $this->doRequest($uri, 'DELETE', array(), $headers);
	}
}

?>