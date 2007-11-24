<?php

require_once('PHPUnit/Framework.php');

class Record_Test extends PHPUnit_Framework_TestCase
{
	protected $record;
	
	public function setUp()
	{
		require_once('lib/TestPhpcouchDummyConnection.class.php');
		require_once('lib/TestPhpcouchRecord.class.php');
		
		$this->record = new TestPhpcouchRecord(new TestPhpcouchDummyConnection(array()));
	}
	
	public function testGetConnection()
	{
		$ret = $this->record->getConnection();
		
		$c = new PHPUnit_Framework_Constraint_IsInstanceOf('PhpcouchConnection');
		$this->assertThat($ret, $c);
	}
	
	public function testSetConnection()
	{
		$con = new TestPhpcouchDummyConnection(array());
		
		$this->record->setConnection($con);
		
		$this->assertSame($con, $this->record->getConnection());
	}
	
	public function testSetDefaultConnection()
	{
		$cname = uniqid();
		$con = new TestPhpcouchDummyConnection(array());
		Phpcouch::registerConnection($cname, $con, true);
		
		$this->record->setConnection();
		
		$this->assertSame($con, $this->record->getConnection());
		
		Phpcouch::unregisterConnection($cname);
	}
	
	public function testIsInitiallyEmpty()
	{
		$this->assertEquals(array('_id' => null), $this->record->toArray());
	}
	
	public function testOverloads()
	{
		$this->assertNull($this->record->zomg);
		$this->assertFalse(isset($this->record->zomg));
		
		$this->record->zomg = 'lol';
		
		$this->assertEquals('lol', $this->record->zomg);
		$this->assertTrue(isset($this->record->zomg));
		
		unset($this->record->zomg);
		
		$this->assertNull($this->record->zomg);
		$this->assertFalse(isset($this->record->zomg));
	}
	
	public function testFromArray()
	{
		$this->record->foo = 'foo';
		$this->record->bar = 'bar';
		
		$this->record->fromArray(array('bar' => 'baz', 'baz' => 'baz'));
		
		$this->assertEquals(array('_id' => null, 'foo' => 'foo', 'bar' => 'baz', 'baz' => 'baz'), $this->record->toArray());
	}
	
	public function testToArray()
	{
		$this->record->_id = '123';
		$this->record->foo = 'foo';
		$this->record->bar = 'bar';
		
		$this->assertEquals(array('_id' => '123', 'foo' => 'foo', 'bar' => 'bar'), $this->record->toArray());
	}
	
	public function testHydrate()
	{
		$this->record->hydrate(array('foo' => 'bar'));
		
		$this->assertEquals(array('_id' => null, 'foo' => 'bar'), $this->record->toArray());
	}
	
	public function testHydrateFromObject()
	{
		$x = new stdClass();
		$x->foo = 'foo';
		$x->bar = 'bar';
		
		$this->record->hydrate($x);
		
		$this->assertEquals(array('_id' => null, 'foo' => 'foo', 'bar' => 'bar'), $this->record->toArray());
	}
	
	public function testHydrateFromRecord()
	{
		$x = new TestPhpcouchRecord(new TestPhpcouchDummyConnection(array()));
		$x->foo = 'foo';
		$x->bar = 'bar';
		
		$this->record->hydrate($x);
		
		$this->assertEquals(array('_id' => null, 'foo' => 'foo', 'bar' => 'bar'), $this->record->toArray());
	}
}

?>