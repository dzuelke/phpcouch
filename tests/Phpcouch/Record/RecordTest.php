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
class RecordTest extends PHPUnit_Framework_TestCase
{
	/**
	 * setup
	 */
	public function setUp()
	{
		Phpcouch::bootstrap();
	}
	
	/**
	 * Tests connection get/set
	 *
	 * @assert       same
	 *
	 * @author       Simon Thulbourn <simon+github@thulbournm.com>
	 */
	public function testConnection()
	{
		$connection = $this->getMockBuilder('\phpcouch\connection\Connection')
										->disableOriginalConstructor()
										->getMock();
		
		$record = new record\Record($connection);
		
		$this->assertSame($record->getConnection(), $connection);
	}
	
	/**
	 * Tests overloading
	 *
	 * @assert       equals
	 * @assert       null
	 *
	 * @author       Simon Thulbourn <simon+github@thulbournm.com>
	 */
	public function testOverload()
	{
		$data = array(
			'foo' => 'bar',
			'baz' => 'buzz'
		);
		
		$connection = $this->getMockBuilder('\phpcouch\connection\Connection')
										->disableOriginalConstructor()
										->getMock();
		
		$record = new record\Record($connection);
		
		foreach($data as $k => $v) {
			$record->{$k} = $v;
			$this->assertEquals($record->{$k}, $v);
			
			unset($record->{$k});
			$this->assertNull($record->{$k});
		}
	}
	
	/**
	 * Tests AccessAccess interface
	 *
	 * @assert       equals
	 * @assert       null
	 *
	 * @author       Simon Thulbourn <simon+github@thulbournm.com>
	 */
	public function testArrayAccess()
	{
		$data = array(
			'foo' => 'bar',
			'baz' => 'buzz'
		);
		
		$connection = $this->getMockBuilder('\phpcouch\connection\Connection')
										->disableOriginalConstructor()
										->getMock();
		
		$record = new record\Record($connection);
		
		foreach($data as $k => $v) {
			$record[$k] = $v;
			$this->assertEquals($record[$k], $v);
			
			unset($record[$k]);
			$this->assertNull($record[$k]);
		}
	}
}

?>