<?php

class PhpcouchServerException extends PhpcouchException
{
	protected $status;
	
	public function __construct(PhpcouchStatus $status)
	{
		parent::__construct($status->error->id, $status->error->reason);
		
		$this->status = $status;
	}
	
	public function getCouchdbError()
	{
		return $this->status;
	}
	
	public function __toString()
	{
		return json_encode($this->status);
	}
}

?>