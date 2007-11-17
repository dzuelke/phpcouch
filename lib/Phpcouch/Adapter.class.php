<?php

abstract class PhpcouchAdapter
{
	public function __construct(array $options = array())
	{
	}
	
	abstract public function put($url, $data = null);
	
	abstract public function get($url);
	
	abstract public function post($url, $data = null);
	
	abstract public function delete($url);
}

?>