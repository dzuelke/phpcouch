<?php

/**
 * Interface for HTTP client adapters.
 *
 * @package    PHPCouch
 *
 * @author     David Zülke <dz@bitxtender.com>
 * @copyright  bitXtender GbR
 *
 * @since      1.0.0
 *
 * @version    $Id$
 */
interface PhpcouchIAdapter
{
	/**
	 * Adapter constructor.
	 *
	 * @param      array An array of initialization options for the driver implementation.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function __construct(array $options = array());
	
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
	public function put($url, $data = null);
	
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
	public function get($url);
	
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
	public function post($url, $data = null);
	
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
	public function delete($url);
}

?>