<?php

class PhpcouchConnection
{
	protected $database = null;
	
	public function __construct($database)
	{
		$this->database = $database;
	}
	
	public function getDatabase()
	{
		return $this->database;
	}
	
	public function setDatabase($database)
	{
		$oldDatabase = $this->database;
		$this->database = $database;
		return $oldDatabase;
	}
	
	public function create(PhpcouchDocument $document)
	{
		
	}
	
	public function retrieve($id, $revision = null)
	{
		
	}
	
	public function update(PhpcouchDocument $document)
	{
		
	}
	
	public function delete($id)
	{
		if($id instanceof PhpcouchDocument) {
			$id = $id->_id;
		}
	}
}

?>