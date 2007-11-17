<?php

class PhpcouchDatabase
{
	protected $db_name = null;
	protected $doc_count = null;
	protected $update_seq = null;
	
	public function __construct($name, $documentCount = null, $updateSequence = null)
	{
		$this->db_name = $name;
		$this->doc_count = $documentCount;
		$this->update_seq = $updateSequence;
	}
	
	public function __toString()
	{
		return $this->db_name;
	}
	
	public function getName()
	{
		return $this->db_name;
	}
	
	public function getDocumentCount()
	{
		return $this->doc_count;
	}
	
	public function getUpdateSequence()
	{
		return $this->update_seq;
	}
	
	public function listDocuments()
	{
		
	}
}

?>