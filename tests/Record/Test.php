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
	
	public function testIsInitiallyEmpty()
	{
		$this->assertEquals(array(), $this->record->toArray());
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
		
		$this->assertEquals(array('foo' => 'foo', 'bar' => 'baz', 'baz' => 'baz'), $this->record->toArray());
	}
	
	public function testToArray()
	{
		$this->record->foo = 'foo';
		$this->record->bar = 'bar';
		
		$this->assertEquals(array('foo' => 'foo', 'bar' => 'bar'), $this->record->toArray());
	}
	
	public function testHydrate()
	{
		$this->record->hydrate(array('foo' => 'bar'));
		
		$this->assertEquals(array('foo' => 'bar'), $this->record->toArray());
	}
	
	public function testHydrateFromObject()
	{
		$x = new stdClass();
		$x->foo = 'foo';
		$x->bar = 'bar';
		
		$this->record->hydrate($x);
		
		$this->assertEquals(array('foo' => 'foo', 'bar' => 'bar'), $this->record->toArray());
	}
	
	public function testHydrateFromRecord()
	{
		$x = new TestPhpcouchRecord(new TestPhpcouchDummyConnection(array()));
		$x->foo = 'foo';
		$x->bar = 'bar';
		
		$this->record->hydrate($x);
		
		$this->assertEquals(array('foo' => 'foo', 'bar' => 'bar'), $this->record->toArray());
	}
}

?>