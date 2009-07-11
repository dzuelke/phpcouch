<?php

namespace phpcouch\http;

class HttpRequest extends HttpMessage
{
	const METHOD_DELETE = 'DELETE';
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	
	protected $destination;
	
	public function __construct($destination = null, $method = self::METHOD_GET)
	{
		if($destination) {
			$this->setDestination($destination);
		}
		$this->setMethod($method);
	}
	
	public function getDestination()
	{
		return $this->destination;
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function setDestination($destination)
	{
		$this->destination = $destination;
	}
	
	public function setMethod($method)
	{
		$this->method = $method;
	}
}

?>