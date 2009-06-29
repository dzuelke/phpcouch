<?php

namespace \phpcouch\adapter;

/**
 * A test adapter using Zend_HTTP
 * 
 * @package    PHPCouch
 * 
 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
 * @copyright  Bitextender GmbH
 * 
 * @since      1.0.0
 * 
 * @version    $Id$
 */
class Test implements AdapterInterface
{
	protected $options = array();
	
	/**
	 * @var        string Response  data
	 */
	protected $reponse = null;
	
	
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
	 * Perform an HTTP PUT request.
	 *
	 * @param      string The URL to call.
	 * @param      array  The JSON data to serialize and send.
	 *
	 * @return     stdClass The JSON response.
	 *
	 * @throws     PhpcouchException ?
	 *
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 * @since      1.0.0
	 */
	public function put($url, $data = null)
	{
		if ($response === null) {
			throw new \phpcouch\Exception('No response set');
		}
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$client = new Zend_Http_Client_Adapter($url, array(
				'adapter' => $adapter
			));
			
		$adapter->setResponse($this->response);
		
		$client->setParameterPost($data);
		
		$response = $client->request('PUT');
		
		$this->clearResponse();
		
		return $response;
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
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 * @since      1.0.0
	 */
	public function get($url)
	{
		if ($response === null) {
			throw new \phpcouch\Exception('No response set');
		}
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$client = new Zend_Http_Client_Adapter($url, array(
				'adapter' => $adapter
			));
			
		$adapter->setResponse($this->response);
		
		$response = $client->request('GET');
		
		$this->clearResponse();
		
		return $response;
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
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 * @since      1.0.0
	 */
	public function post($url, $data = null)
	{
		if ($response === null) {
			throw new \phpcouch\Exception('No response set');
		}
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$client = new Zend_Http_Client_Adapter($url, array(
				'adapter' => $adapter
			));
			
		$adapter->setResponse($this->response);
		
		$client->setParameterPost($data);
		
		$response = $client->request('POST');
		
		$this->clearResponse();
		
		return $response;
	}
	
	/**
	 * Perform an HTTP DELETE request.
	 *
	 * @param      string The URL to call.
	 * @param      array HTTP headers
	 *
	 * @return     stdClass The JSON response.
	 *
	 * @throws     PhpcouchException ?
	 *
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 *
	 * @since      1.0.0
	 */
	public function delete($url, $headers = array())
	{
		if ($response === null) {
			throw new \phpcouch\Exception('No response set');
		}
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$client = new Zend_Http_Client_Adapter($url, array(
				'adapter' => $adapter
			));
			
		$adapter->setResponse($this->response);
		
		if (sizeof($headers) !== 0) {
			$client->setHeaders($headers);
		}
		
		$response = $client->request('DELETE');

		return $response;
	}
	
	/**
	 * Set response data for the adapter
	 *
	 * @param      string The URL to call.
	 *
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 *
	 * @since      1.0.0
	 */
	public function setResponse($reponse)
	{
		$this->response = $response;
	}
	
	/**
	 * Clear response data for the adapter
	 *
	 * @param      string The URL to call.
	 *
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 *
	 * @since      1.0.0
	 */
	public function clearResponse()
	{
		$this->response = null;
	}
}


?>