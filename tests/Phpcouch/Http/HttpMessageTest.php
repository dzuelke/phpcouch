<?php

use phpcouch\Phpcouch;
use phpcouch\Exception;
use phpcouch\connection;
use phpcouch\adapter;

/**
 * Phpcouch Test
 *
 * @package    PHPCouch
 * @subpackage Tests
 *
 * @author     Simon Thulbourn <simon+github@thulbourn.com>
 * @copyright  authors
 *
 * @since      1.0.0
 */
class HttpMessageTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup
	 */
	public function setUp()
	{
		Phpcouch::bootstrap();
	}
	
	/**
	 * Test header functionality
	 * 
	 * @assert     true
	 * @assert     equals
	 * @assert     empty
	 * 
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testHeader()
	{
		$message = new \phpcouch\http\HttpMessage();
		
		$data = array(
			// input => expected
			'content-type' => 'Content-Type',
			'etag' => 'ETag',
			'wWw-authEnticate' => 'WWW-Authenticate'
		);
		
		foreach ($data as $k => $v) {
			$this->assertEquals($message->normalizeHttpHeaderName($k), $v);
		}
		
		$message->setHeader('Content-Type', 'text/plain; charset=utf-8');
		$this->assertTrue($message->hasHeader('Content-Type'));
		$this->assertEquals($message->getHeader('Content-Type'), array('text/plain; charset=utf-8'));
		
		$message->setHeader('Content-Type', 'application/json; charset=utf-8', false);
		$this->assertEquals($message->getHeader('Content-Type'), array('text/plain; charset=utf-8'));
		
		$this->assertEquals($message->removeHeader('Content-Type'), array('text/plain; charset=utf-8'));
		$this->assertEmpty($message->getHeaders(), array());
		
		$data = array(
			'Content-Type' => 'text/plain; charset=utf-8',
			'Content-Length' => 0
		);
		
		foreach ($data as $k => &$v) {
			$message->setHeader($k, $v, true);
			
			$v = array($v);
		}
		
		$this->assertEquals($message->getHeaders(), $data);
		
		$message->clearHeaders();
		$this->assertEmpty($message->getHeaders());
	}
	
	/**
	 * Test content type
	 * 
	 * @assert     equals
	 * 
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testContentType()
	{
		$message = new \phpcouch\http\HttpMessage();
		
		$message->setContentType('text/plain; charset=utf-8');
		$this->assertEquals($message->getContentType(), 'text/plain; charset=utf-8');
	}
	
	/**
	 * Test content
	 * 
	 * @assert     equals
	 * 
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testContent()
	{
		$content = 'The quick brown fox jumps over the lazy dog';
		
		$message = new \phpcouch\http\HttpMessage();
		
		$message->setContent($content);
		$this->assertEquals($message->getContent(), $content);
		
		$this->assertEquals($message->getContentSize(), strlen($content));
		
		$message->clearContent();
		$this->assertEmpty($message->getContent());
	}
}

?>