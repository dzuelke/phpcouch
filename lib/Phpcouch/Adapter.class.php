<?php

interface PhpcouchIAdapter
{
	public function __construct(array $options = array());
	
	public function put($url, $data = null);
	
	public function get($url);
	
	public function post($url, $data = null);
	
	public function delete($url);
}

?>