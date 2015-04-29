<?php

namespace phpcouch\http;

class HttpErrorException extends \RuntimeException implements \phpcouch\Exception
{
	/**
	 * @var HttpResponse
	 */
	private $response;
	
	/**
	 * @param string       $message
	 * @param int          $code
	 * @param HttpResponse $response
	 * @param \Exception   $previous
	 */
	public function __construct($message, $code, \phpcouch\http\HttpResponse $response = null, \Exception $previous = null)
	{
		$message = $message . "\n" . $response->getContent();
		
		parent::__construct($message, $code, $previous);
		
		$this->response = $response;
	}
	
	/**
	 * @return HttpResponse
	 */
	public function getResponse()
	{
		return $this->response;
	}
}

?>