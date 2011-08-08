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
class HttpResponseTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		Phpcouch::bootstrap();
	}
	
	/**
	 * Test Preceeding Response
	 *
	 * @assert       same
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testPrecedingResponse()
	{
		$precedingResponse = new \phpcouch\http\HttpResponse();
		$precedingResponse->setStatusCode(100);
		
		$response = new \phpcouch\http\HttpResponse();
		$response->setPrecedingResponse($precedingResponse);
		
		$this->assertSame($response->getPrecedingResponse(), $precedingResponse);
	}
	
	/**
	 * Test Status code
	 *
	 * @assert       equals
	 * @assert       true
	 * @assert       false
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testStatusCode()
	{
		$response = new \phpcouch\http\HttpResponse();
		$response->setStatusCode(100);
		
		$this->assertEquals($response->getStatusCode(100), 100);
		
		$this->assertTrue($response->validateHttpStatusCode(100));
		$this->assertFalse($response->validateHttpStatusCode(1));
	}
}

?>