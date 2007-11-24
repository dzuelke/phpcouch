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
	
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}
	
	public function __unset($name)
	{
		if(array_key_exists($name, $this->data)) {
			unset($this->data[$name]);
		}
	}
	
	public function hydrate($data)
	{
		$this->data = array();
		
		$this->fromArray($data);
	}
	
	public function fromArray($data)
	{
		if($data instanceof PhpcouchIRecord) {
			$data = $data->toArray();
		} elseif(is_object($data)) {
			$data = get_object_vars($data);
		}
		
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
	
	public function setConnection(PhpcouchConnection $connection = null)
	{
		if($connection === null) {
			$connection = Phpcouch::getConnection();
		}
		
		$this->connection = $connection;
	}
}

?>