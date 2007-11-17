<?php

class PhpcouchZendhttpclientAdapter extends PhpcouchAdapter
{
	public function __construct(array $options = array())
	{
		parent::__construct($options);
		
		if(!class_exists('Zend_Http_Client')) {
			require('Zend/Http/Client.php');
		}
		
		$options = array_merge(array(
			'keepalive'    => true,
			'useragent'    => Phpcouch::getVersionInfo(),
		), $options);
		
		$this->client = new Zend_Http_Client();
		$this->client->setConfig($options);
		$this->client->setEncType('application/json');
	}
	
	protected function getClient($reset = true)
	{
		if($reset) {
			$this->client->resetParameters();
		}
		return $this->client;
	}
	
	protected function doRequest($method = 'GET')
	{
		try {
			$r = $this->getClient()->request('PUT');
		} catch(Zend_Http_Client_Exception $e) {
			throw new PhpcouchAdapterException($e->getMessage());
		}
		
		if($r->isError()) {
			if($r->getStatus() % 500 < 100) {
				throw new PhpcouchServerErrorException();
			} else {
				throw new PhpcouchClientErrorException();
			}
		} elseif($r->isRedirect()) {
			throw new PhpcouchAdapterException('Too many redirects');
		} else {
			return $r->getBody();
		}
	}
	
	public function put($uri, $data)
	{
		$c = $this->getClient();
		$c->setUri($uri);
		$c->setRawData($data);
		
		return $this->doRequest('PUT');
	}
	
	public function get($uri)
	{
		$c = $this->getClient();
		$c->setUri($uri);
		
		return $this->doRequest('GET');
	}
	
	public function post($uri, $data)
	{
		$c = $this->getClient();
		$c->setUri($uri);
		$c->setRawData($data);
		
		return $this->doRequest('POST');
	}
	
	public function delete($url)
	{
		$c = $this->getClient();
		$c->setUri($uri);
		
		return $this->doRequest('DELETE');
	}
}

?>