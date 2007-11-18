<?php

abstract class PhpcouchRecord implements PhpcouchIRecord
{
	protected $connection = null;
	protected $data = array();
	
	public function __construct(PhpcouchConnection $connection = null)
	{
		$this->setConnection($connection);
	}
	
	public function __get($name)
	{
		if(isset($this->data[$name])) {
			return $this->data[$name];
		}
	}
	
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}
	
	public function hydrate($data)
	{
		if(is_object($data)) {
			$data = get_object_vars($data);
		}
		
		$this->fromArray($data);
	}
	
	public function dehydrate()
	{
		return $this->toArray();
	}
	
	public function fromArray(array $data)
	{
		foreach($data as $key => $value) {
			if(strpos($key, '_') === 0) {
				$this->{$key} = $value;
			} else {
				$this->__set($key, $value);
			}
		}
	}
	
	public function toArray()
	{
		$retval = array();
		
		foreach($this->data as $key => $value) {
			$retval[$key] = $this->__get($key);
		}
		
		foreach(get_object_vars($this) as $key => $value) {
			if(strpos($key, '_') === 0) {
				$retval[$key] = $value;
			}
		}
		
		return $retval;
	}
	
	public function getConnection()
	{
		return $this->connection;
	}
	
	public function setConnection(PhpcouchConnection $connection)
	{
		if($connection === null) {
			$connection = Phpcouch::getConnection();
		}
		
		$this->connection = $connection;
	}
}

?>