<?php

class PhpcouchErrorException extends PhpcouchException
{
	protected $response = null;
	
	public function __construct($message, $status = null, $response = null)
	{
		parent::__construct($message, $status);
		
		$this->response = $response;
	}
	
	public function getResponse()
	{
		return json_decode($this->response);
	}
}

?>