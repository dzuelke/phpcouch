<?php

abstract class PhpcouchMutableRecord extends PhpcouchRecord
{
	public function __set($name, $value)
	{
		if(!isset($this->data[$name]) || $this->data[$name] !== $value) {
			$this->isModified = true;
		}
		$this->data[$name] = $value;
	}
	
	public function __unset($name)
	{
		if(array_key_exists($this->data[$name])) {
			unset($this->data[$name]);
		}
	}
}

?>