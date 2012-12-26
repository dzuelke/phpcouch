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
	 * @param      \phpcouch\http\HttpRequest The request to send.
	 *
	 * @return     \phpcouch\http\HttpResponse  The response from the server
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function sendRequest(\phpcouch\http\HttpRequest $request);
}

?>