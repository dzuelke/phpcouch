<?php

class PhpcouchCurlAdapter extends PhpcouchAdapter
{
	protected function makeCurl($uri);
	{
		$r = curl_init($uri);
	}
	
	public function put($json, array $info = array(), array $options = array())
	{
		$uri = $this->buildUri($info, $options);
		
		$r = $this->makeCurl($uri);
		curl_setopt($r, CURLOPT_CUSTOMREQUEST, 'PUT');
		
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
		
		$r = $this->makeCurl($uri);
		curl_setopt($r, CURLOPT_CUSTOMREQUEST, 'GET');
		
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
		
		$r = $this->makeCurl($uri);
		curl_setopt($r, CURLOPT_CUSTOMREQUEST, 'POST');
		
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
		
		$r = $this->makeCurl($uri);
		curl_setopt($r, CURLOPT_CUSTOMREQUEST, 'DELETE');
		
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