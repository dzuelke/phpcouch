<?php

interface PhpcouchIRecord
{
	public function __construct(PhpcouchConnection $connection = null);
	
	public function __get($name);
	public function __isset($name);
	
	public function hydrate($data);
	public function dehydrate();
	
	public function getConnection();
	public function setConnection(PhpcouchConnection $connection = null);
}

?>