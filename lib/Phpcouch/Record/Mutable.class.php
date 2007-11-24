<?php

abstract class PhpcouchMutableRecord extends PhpcouchRecord implements PhpcouchIMutableRecord
{
	protected $isNew = true;
	protected $isModified = false;
	
	public function isNew()
	{
		return $this->isNew;
	}
	
	public function isModified()
	{
		return $this->isModified;
	}
	
	public function __set($name, $value)
	{
		if(!isset($this->data[$name]) || $this->data[$name] !== $value) {
			$this->isModified = true;
		}
		
		parent::__set($name, $value);
	}
	
	public function __unset($name)
	{
		if(array_key_exists($name, $this->data)) {
			$this->isModified = true;
		}
		
		parent::__unset($name);
	}
	
	public function hydrate($data)
	{
		parent::hydrate($data);
		
		$this->isNew = false;
		$this->isModified = false;
	}
	
	public function dehydrate()
	{
		return $this->toArray();
	}
}

?>