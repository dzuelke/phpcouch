<?php

namespace phpcouch\http;

class HttpErrorException extends \RuntimeException implements \phpcouch\Exception
{
	private $response;
	
	public function __construct($message, $code, \phpcouch\http\HttpResponse $response = null, \Exception $previous = null)
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