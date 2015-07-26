<?php

use phpcouch\Phpcouch;
use phpcouch\Exception;
use phpcouch\connection;
use phpcouch\adapter;
use phpcouch\record;

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
class MutableRecordAbstractTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup
	 */
	public function setUp()
	{
		Phpcouch::bootstrap();
	}
	
	/**
	 * Test hydration
	 *
	 * @assert     equals
	 *
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testHydration()
	{
		$data = array(
			'foo' => 'bar',
			'baz' => 'buzz',
		);
		
		$mutableRecord = $this->getMockBuilder('\phpcouch\record\MutableRecordAbstract')
											->disableOriginalConstructor()
											->getMockForAbstractClass();
		$connection = $this->getMockBuilder('\phpcouch\connection\Connection')
										->disableOriginalConstructor()
										->getMock();	
		$mutableRecord->setConnection($connection);
		
		$mutableRecord->hydrate($data);
		
		$this->assertEquals($mutableRecord->dehydrate(), $data);
	}
	
	/**
	 * Test overloading
	 *
	 * @assert     equals
	 * @assert     null
	 *
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testOverloading()
	{
		$data = array(
			'foo' => 'bar',
			'baz' => 'buzz',
		);
		
		$mutableRecord = $this->getMockBuilder('\phpcouch\record\MutableRecordAbstract')
											->disableOriginalConstructor()
											->getMockForAbstractClass();
		
		foreach ($data as $k => $v) {
			$mutableRecord->{$k} = $v;
			$this->assertEquals($mutableRecord->{$k}, $v);
			
			unset($mutableRecord->{$k});
			$this->assertNull($mutableRecord->{$k});
		}
	}
	
	/**
	 * Test states
	 *
	 * @assert     equals
	 * @assert     null
	 *
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testStates()
	{
		$data = array(
			'foo' => 'bar',
			'baz' => 'buzz',
		);
		
		$mutableRecord = $this->getMockBuilder('\phpcouch\record\MutableRecordAbstract')
											->disableOriginalConstructor()
											->getMockForAbstractClass();
											
		$mutableRecord->hydrate($data);
		
		
		// false since this should be only used when data is returned
		// from the database.
		$this->assertFalse($mutableRecord->isModified());
		
		// modify record
		$mutableRecord->foo = 'baz';
		$this->assertTrue($mutableRecord->isModified());
	}
}

?>