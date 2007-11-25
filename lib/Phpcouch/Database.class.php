<?php

class PhpcouchDatabase extends PhpcouchRecord
{
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
}

?>