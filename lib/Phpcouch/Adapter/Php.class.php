<?php

/**
 * An adapter implemented using the PHP >= 5.3 fopen wrapper.
 *
 * @package    PHPCouch
 * @subpackage Adapter
 *
 * @author     David Zülke <dz@bitxtender.com>
 * @copyright  bitXtender GbR
 *
 * @since      1.0.0
 *
 * @version    $Id$
 */
class PhpcouchPhpAdapter implements PhpcouchIAdapter
{
	/**
	 * Adapter constructor.
	 *
	 * @param      array An array of initialization options for this driver implementation.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function __construct(array $options = array())
	{
	}
	
	/**
	 * Get the client class instance.
	 *
	 * @param      bool Whether or not to reset the client before it is returned (default true).
	 *
	 * @return     Zend_Http_Client The client instance.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	protected function getClient($reset = true)
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
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	protected function doRequest($uri, $method = 'GET', $data = null)
	{
		$c = $this->getClient();
		
		$c->setUri($uri);
		
		if($data !== null) {
			// data to send, let's encode it to JSON
			// must set the content type, since it's reset otherwise (fantastic implementation, Zend...)
			$data = $c->setRawData(json_encode($data), 'application/json');
		}
		
		try {
			// perform the request
			$r = $c->request($method);
		} catch(Zend_Http_Client_Exception $e) {
			// something went wrong; wrap the exception and throw again
			// this is typically a timeout, unknown host etc, not a 404 or such
			throw new PhpcouchAdapterException($e->getMessage());
		}
		
		if($r->isError()) {
			if($r->getStatus() % 500 < 100) {
				// a 5xx response
				throw new PhpcouchServerErrorException($r->getMessage(), $r->getStatus(), json_decode($r->getBody()));
			} else {
				// a 4xx response
				throw new PhpcouchClientErrorException($r->getMessage(), $r->getStatus(), json_decode($r->getBody()));
			}
		} elseif($r->isRedirect()) {
			// by default, we're following up to five redirects, so we never see them in the response, unless... there were too many
			throw new PhpcouchAdapterException('Too many redirects');
		} else {
			// finally, decode the JSON body and return it
			return json_decode($r->getBody());
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
	 * @author     David Zülke <dz@bitxtender.com>
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
	 * @author     David Zülke <dz@bitxtender.com>
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
	 * @author     David Zülke <dz@bitxtender.com>
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
	 *
	 * @return     stdClass The JSON response.
	 *
	 * @throws     PhpcouchException ?
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function delete($uri)
	{
		return $this->doRequest($uri, 'DELETE');
	}
}

?>