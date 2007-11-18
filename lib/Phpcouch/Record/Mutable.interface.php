<?php

interface PhpcouchIMutableRecord extends PhpcouchIRecord
{
	public function __set($name, $value);
	public function __unset($name);
}

?>