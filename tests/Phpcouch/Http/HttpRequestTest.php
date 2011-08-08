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
class HttpRequestTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup
	 */
	public function setUp()
	{
		Phpcouch::bootstrap();
	}
	
	/**
	 * test constructor
	 *
	 * @assert       null
	 * @assert       equals
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testConstructor()
	{
		$request = new \phpcouch\http\HttpRequest();
		$this->assertNull($request->getDestination());
		$this->assertEquals($request->getMethod(), 'GET');
		
		$request = new \phpcouch\http\HttpRequest('http://example.org', \phpcouch\http\HttpRequest::METHOD_DELETE);
		$this->assertEquals($request->getDestination(), 'http://example.org');
		$this->assertEquals($request->getMethod(), 'DELETE');
	}
	
	/**
	 * Test set destination
	 *
	 * @assert       equals
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testSetDestination()
	{
		$request = new \phpcouch\http\HttpRequest();
		$request->setDestination('http://example.org');
		
		$this->assertEquals($request->getDestination(), 'http://example.org');
	}
	
	/**
	 * Test set method
	 *
	 * @assert       equals
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testSetMethod()
	{
		$request = new \phpcouch\http\HttpRequest();
		$request->setMethod(\phpcouch\http\HttpRequest::METHOD_DELETE);
		
		$this->assertEquals($request->getMethod(), 'DELETE');
	}
}

?>