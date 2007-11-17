<?php

class PhpcouchZendhttpclientAdapter implements PhpcouchIAdapter
{
	public function __construct(array $options = array())
	{
		if(!class_exists('Zend_Http_Client')) {
			require('Zend/Http/Client.php');
		}
		
		$options = array_merge(array(
			'keepalive'    => true,
			'useragent'    => Phpcouch::getVersionString(),
		), $options);
		
		$this->client = new Zend_Http_Client();
		$this->client->setConfig($options);
	}
	
	protected function getClient($reset = true)
	{
		if($reset) {
			$this->client->resetParameters();
		}
		return $this->client;
	}
	
	protected function doRequest($uri, $method = 'GET', $data = null)
	{
		$c = $this->getClient();
		
		$c->setUri($uri);
		
		if($data !== null) {
			$data = $c->setRawData(json_encode($data), 'application/json');
		}
		
		try {
			$r = $c->request($method);
		} catch(Zend_Http_Client_Exception $e) {
			throw new PhpcouchAdapterException($e->getMessage());
		}
		
		if($r->isError()) {
			if($r->getStatus() % 500 < 100) {
				throw new PhpcouchServerErrorException($r->getMessage(), $r->getStatus(), json_decode($r->getBody()));
			} else {
				throw new PhpcouchClientErrorException($r->getMessage(), $r->getStatus(), json_decode($r->getBody()));
			}
		} elseif($r->isRedirect()) {
			throw new PhpcouchAdapterException('Too many redirects');
		} else {
			return json_decode($r->getBody());
		}
	}
	
	public function put($uri, $data = null)
	{
		return $this->doRequest($uri, 'PUT', $data);
	}
	
	public function get($uri)
	{
		return $this->doRequest($uri, 'GET');
	}
	
	public function post($uri, $data = null)
	{
		return $this->doRequest($uri, 'POST', $data);
	}
	
	public function delete($uri)
	{
		return $this->doRequest($uri, 'DELETE');
	}
}

?>