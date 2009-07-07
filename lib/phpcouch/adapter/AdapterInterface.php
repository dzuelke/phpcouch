<?php

namespace phpcouch\adapter;

/**
 * Interface for HTTP client adapters.
 *
 * @package    PHPCouch
 *
 * @author     David Zülke <david.zuelke@bitextender.com>
 * @copyright  Bitextender GmbH
 *
 * @since      1.0.0
 *
 * @version    $Id$
 */
interface AdapterInterface
{
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
	 * @since      1.0.0
	 */
	public function sendRequest($method, $url, array $headers = array(), $payload = null);
}

?>