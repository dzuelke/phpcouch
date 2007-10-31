<?php

class PhpcouchDocument
{
	protected $_connection = null;
	protected $_data = array();
	
	public function __construct(PhpcouchConnection $connection = null)
	{
		if($connection === null) {
			$connection = Phpcouch::getConnection();
		}
		
		$this->_connection = $connection;
	}
	
	public function __get($name)
	{
		if(isset($this->_data[$name])) {
			return $this->_data[$name];
		}
	}
	
	public function __isset($name)
	{
		return isset($this->_data[$name]);
	}
	
	public function __set($name, $value)
	{
		$this->_data[$name] = $value;
	}
	
	public function hydrate($data)
	{
		if(is_array($data)) {
			$this->fromArray($data);
		} elseif(is_object($data)) {
			$this->fromArray(get_object_vars($data));
		}
	}
	
	public function fromArray(array $data)
	{
		foreach($data as $key => $value) {
			$this->__set($key, $value);
		}
	}
	
	public function toArray(array $data)
	{
		$retval = array();
		
		foreach($this->_data as $key => $value) {
			$retval[$key] = $this->__get($key);
		}
	}
	
	public function retrieveAttachmentList()
	{
		
	}
	
	public function retrieveAttachment($name)
	{
		
	}
	
	public function retrieveRevision($revision)
	{
		
	}
	
	public function retrieveRevisionStatus($revision)
	{
		
	}
	
	public function retrieveRevisionList()
	{
		
	}
	
	public function retrieveRevisionInfoList()
	{
		
	}
}

?>