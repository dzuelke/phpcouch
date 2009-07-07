<?php

namespace phpcouch\http;

class HttpMessage
{
	/**
	 * @var        mixed The message content.
	 */
	protected $content;
	
	/**
	 * @var        array The HTTP headers scheduled to be sent with the message.
	 */
	protected $httpHeaders = array();
	
	/**
	 * Clear the content for this message.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function clearContent()
	{
		$this->content = null;
	}
	
	/**
	 * Clears the HTTP headers set for this message.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function clearHttpHeaders()
	{
		$this->httpHeaders = array();
	}
	
	/**
	 * Retrieve the content set for this message.
	 *
	 * @return     mixed The content set in this message.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getContent()
	{
		return $this->content;
	}
	
	/**
	 * Retrieve the size (in bytes) of the content set for this message.
	 *
	 * @return     int The content size in bytes.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getContentSize()
	{
		if(is_resource($this->content)) {
			if(($stat = fstat($this->content)) !== false) {
				return $stat['size'];
			} else {
				return false;
			}
		} else {
			return strlen($this->content);
		}
	}
	
	/**
	 * Retrieve the content type set for the message.
	 *
	 * @return     string A content type, or null if none is set.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getContentType()
	{
		$retval = $this->getHttpHeader('Content-Type');
		if(is_array($retval) && count($retval)) {
			return $retval[0];
		} else {
			return null;
		}
	}
	
	/**
	 * Retrieve the HTTP header values set for the message.
	 *
	 * @param      string A HTTP header field name.
	 *
	 * @return     array All values set for that header, or null if no headers set
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getHttpHeader($name)
	{
		$name = $this->normalizeHttpHeaderName($name);
		$retval = null;
		if(isset($this->httpHeaders[$name])) {
			$retval = $this->httpHeaders[$name];
		}
		return $retval;
	}
	
	/**
	 * Retrieve the HTTP headers set for the message.
	 *
	 * @return     array An associative array of HTTP header names and values.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getHttpHeaders()
	{
		return $this->httpHeaders;
	}
	
	/**
	 * Check whether or not some content is set.
	 *
	 * @return     bool If any content is set, false otherwise.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function hasContent()
	{
		return $this->content !== null && $this->content !== '';
	}
	
	/**
	 * Check if an HTTP header has been set for the message.
	 *
	 * @param      string A HTTP header field name.
	 *
	 * @return     bool true if the header exists, false otherwise.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function hasHttpHeader($name)
	{
		$name = $this->normalizeHttpHeaderName($name);
		$retval = false;
		if(isset($this->httpHeaders[$name])) {
			$retval = true;
		}
		return $retval;
	}
	
	/**
	 * Normalizes a HTTP header names
	 *
	 * @param      string A HTTP header name
	 *
	 * @return     string A normalized HTTP header name
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function normalizeHttpHeaderName($name)
	{
		if(strtolower($name) == "etag") {
			return "ETag";
		} elseif(strtolower($name) == "www-authenticate") {
			return "WWW-Authenticate";
		} else {
			return str_replace(' ', '-', ucwords(str_replace('-', ' ', strtolower($name))));
		}
	}
	
	/**
	 * Remove the HTTP header set for the message.
	 *
	 * @param      string A HTTP header field name.
	 *
	 * @return     mixed The removed header's value or null if header was not set.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function removeHttpHeader($name)
	{
		$name = $this->normalizeHttpHeaderName($name);
		$retval = null;
		if(isset($this->httpHeaders[$name])) {
			$retval = $this->httpHeaders[$name];
			unset($this->httpHeaders[$name]);
		}
		return $retval;
	}
	
	/**
	 * Set the content for this message.
	 *
	 * @param      mixed The content to be sent in this message.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}
	
	/**
	 * Set the content type for the message.
	 *
	 * @param      string A content type.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setContentType($type)
	{
		$this->setHttpHeader('Content-Type', $type);
	}
	
	/**
	 * Set a HTTP header for the message
	 *
	 * @param      string A HTTP header field name.
	 * @param      mixed  A HTTP header field value, of an array of values.
	 * @param      bool   If true, a header with that name will be overwritten, otherwise, the value will be appended.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setHttpHeader($name, $value, $replace = true)
	{
		$name = $this->normalizeHttpHeaderName($name);
		if(!isset($this->httpHeaders[$name]) || $replace) {
			$this->httpHeaders[$name] = array();
		}
		if(is_array($value)) {
			$this->httpHeaders[$name] = array_merge($this->httpHeaders[$name], $value);
		} else {
			$this->httpHeaders[$name][] = $value;
		}
	}

}

?>