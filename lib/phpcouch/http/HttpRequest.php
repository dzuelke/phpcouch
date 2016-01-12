<?php

namespace phpcouch\http;

class HttpRequest extends HttpMessage
{
	const METHOD_DELETE = 'DELETE';
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	
	/**
	 * @var        string The destination URL of this request.
	 */
	protected $destination;
	
	/**
	 * @var        string The HTTP request method of this request.
	 */
	protected $method;
	
	/**
	 * Constructor.
	 *
	 * @param      string The destination URL of this request.
	 * @param      string The HTTP request method of this request.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __construct($destination = null, $method = self::METHOD_GET)
	{
		if($destination) {
			$this->setDestination($destination);
		}
		$this->setMethod($method);
	}
	
	/**
	 * Returns the destination URL of this request.
	 *
	 * @return     string The destination URL of this request.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getDestination()
	{
		return $this->destination;
	}
	
	/**
	 * Returns the HTTP request method of this request.
	 *
	 * @return     string The HTTP request method of this request.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getMethod()
	{
		return $this->method;
	}
	
	/**
	 * Sets the destination URL of this request.
	 *
	 * @param      string The destination URL of this request.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setDestination($destination)
	{
		$this->destination = $destination;
	}
	
	/**
	 * Sets the HTTP request method of this request.
	 *
	 * @param      string The HTTP request method of this request.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setMethod($method)
	{
		$this->method = $method;
	}
	
	/**
	 * Set the content for this message.
	 *
	 * @param      mixed The content to be sent in this message.
	 */
	public function setContent($content) {
		if(is_array($content)) {
			// Create a multipart/related request
			$body = '';
			
			$boundary = md5(uniqid(mt_rand(), true));
			
			foreach($content as $i => $part) {
				$body .= '--' . $boundary . "\r\n";
				if($i == 0 && ($contentType = $this->getContentType())) {
					// Use content type for first part only
					$body .= 'Content-Type: ' . $contentType . "\r\n";
				}
				$body .= "\r\n";
				$body .= $part . "\r\n";
			}
			
			$content = $body . '--' . $boundary . '--' . "\r\n";
			$this->setContentType('multipart/related;boundary="' . $boundary . '"');
		}
		
		parent::setContent($content);
	}
}

?>