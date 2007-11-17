<?php

abstract class PhpcouchAdapter
{
	public function __construct(array $options = array())
	{
	}
	
	abstract public function put($url, $data);
	
	abstract public function get($url);
	
	abstract public function post($url, $data);
	
	abstract public function delete($url);
}

?>