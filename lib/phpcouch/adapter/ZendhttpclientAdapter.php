<?php

namespace phpcouch\adapter;

use phpcouch\Exception;

/**
 * An adapter implemented using the Zend Framework HTTP Client.
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
class ZendhttpclientAdapter implements AdapterInterface
{
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
		// by default, we override two options, keepalive (nice when doing multiple requests) and the user agent string
		$options = array_merge(array(
			'keepalive'    => true,
			'useragent'    => \phpcouch\Phpcouch::getVersionString(),
		), $options);
		
		// make a client instance
		// we rely on Zend_Loader's autoloader being active. That's the user's job though
		$this->client = new \Zend_Http_Client();
		// and feed it our options
		$this->client->setConfig($options);
	}
	
	/**
	 * Get the client class instance.
	 *
	 * @param      bool Whether or not to reset the client before it is returned (default true).
	 *
	 * @return     Zend_Http_Client The client instance.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getClient($reset = true)
	{
		if($reset) {
			// "reset" the client
			// this actually only resets params/body and the content-length/content-type headers, but that's enough
			$this->client->resetParameters();
		}
		return $this->client;
	}
	
	/**
	 * Perform the actual request.
	 *
	 * @param      string The URL to call.
	 * @param      string The HTTP method to use.
	 * @param      array  The data to serialize to JSON and send.
	 *
	 * @return     stdClass The unserialized response JSON.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	protected function sendRequest($uri, $method = 'GET', $data = null, $headers = array())
	{
		$c = $this->getClient();
		
		$c->setUri($uri);
		
		if($data !== null) {
			// data to send, let's encode it to JSON
			// must set the content type, since it's reset otherwise (fantastic implementation, Zend...)
			$data = $c->setRawData(json_encode($data), 'application/json');
		}
		
		if (sizeof($headers) !== 0) {
			$c->setHeaders($headers);
		}
		
		try {
			// perform the request
			$r = $c->request($method);
		} catch(\Zend_Http_Client_Exception $e) {
			// something went wrong; wrap the exception and throw again
			// this is typically a timeout, unknown host etc, not a 404 or such
			throw new Exception($e->getMessage());
		}
		
		if($r->isError()) {
			if($r->getStatus() % 500 < 100) {
				// a 5xx response
				throw new Exception($r->getMessage(), $r->getStatus(), json_decode($r->getBody()));
			} else {
				// a 4xx response
				throw new Exception($r->getMessage(), $r->getStatus(), json_decode($r->getBody()));
			}
		} elseif($r->isRedirect()) {
			// by default, we're following up to five redirects, so we never see them in the response, unless... there were too many
			throw new Exception('Too many redirects');
		} else {
			// finally, decode the JSON body and return it
			return json_decode($r->getBody(), true);
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
		return $this->sendRequest($uri, 'PUT', $data);
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
		return $this->sendRequest($uri, 'GET');
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
		return $this->sendRequest($uri, 'POST', $data);
	}
	
	/**
	 * Perform an HTTP DELETE request.
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
	public function delete($uri, $headers = array())
	{
		return $this->sendRequest($uri, 'DELETE', null, $headers);
	}
}

?>