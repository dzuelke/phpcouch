<?php

interface PhpcouchIMutableRecord extends PhpcouchIRecord
{
	public function isNew();
	public function isModified();
	
	public function dehydrate();
	
	public function save();
}

?>