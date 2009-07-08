<?php

namespace phpcouch\adapter;

class HttpErrorException extends TransportException
{
	private $response;
	
	public function __construct($message, $code, \phpcouch\http\HttpResponse $response = null, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
		
		$this->response = $response;
	}
	
	public function getResponse()
	{
		return $this->response;
	}
}

?>