<?php

class PhpcouchZendhttpclientAdapter extends PhpcouchAdapter
{
	public function __construct(array $options)
	{
		parent::__construct($options);
		
		if(!class_exists('Zend_Http_Client')) {
			require('Zend/Http/Client.php');
		}
		
		$this->client = new Zend_Http_Client();
		$this->client->setConfig(array(
			'maxredirects' => 2,
			'timeout' => 30,
			'useragent' => 'PHPCouch',
		));
		$this->client->setEncType('application/json');
	}
	
	public function put($json, array $info = array(), array $options = array())
	{
		$uri = $this->buildUri($info, $options);
		
		$r = new HttpRequest($uri, HTTP_METH_PUT);
		$r->setPutData($json);
		
		try {
			return $r->send()->getBody();
		} catch (HttpException $e) {
			var_dump($e->getMessage());
			var_dump($r->getRawRequestMessage());
			var_dump($r->getRawResponseMessage());
			die('ZOMG PUT');
		}
	}
	
	public function get(array $info, array $options = array())
	{
		$uri = $this->buildUri($info, $options);
		
		$r = new HttpRequest($uri, HTTP_METH_GET);
		
		try {
			return $r->send()->getBody();
		} catch (HttpException $e) {
			var_dump($e->getMessage());
			var_dump($r->getRawRequestMessage());
			var_dump($r->getRawResponseMessage());
			die('ZOMG GET');
		}
	}
	
	public function post($json, array $info = array(), array $options = array())
	{
		$uri = $this->buildUri($info, $options);
		
		$r = new HttpRequest($uri, HTTP_METH_POST);
		$r->setRawPostData($json);
		
		try {
			return $r->send()->getBody();
		} catch (HttpException $e) {
			var_dump($e->getMessage());
			var_dump($r->getRawRequestMessage());
			var_dump($r->getRawResponseMessage());
			die('ZOMG POST');
		}
	}
	
	public function delete(array $info, array $options = array())
	{
		$uri = $this->buildUri($info, $options);
		
		$r = new HttpRequest($uri, HTTP_METH_DELETE);
		
		try {
			return $r->send()->getBody();
		} catch (HttpException $e) {
			var_dump($e->getMessage());
			var_dump($r->getRawRequestMessage());
			var_dump($r->getRawResponseMessage());
			die('ZOMG DELETE');
		}
	}
}

?>