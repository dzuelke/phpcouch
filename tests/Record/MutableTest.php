<?php

class Record_MutableTest extends PHPUnit_Framework_TestCase
{
	protected $record;
	
	public function setUp()
	{
		require_once('lib/Connection/Dummy.class.php');
		require_once('lib/Record/Mutable.class.php');
		
		$this->record = new TestPhpcouchMutableRecord(new TestPhpcouchDummyConnection(array()));
	}
	
	public function testIsInitiallyNew()
	{
		$this->assertTrue($this->record->isNew());
	}
	
	public function testIsInitiallyUnmodified()
	{
		$this->assertFalse($this->record->isModified());
	}
	
	public function testSetFlagsModified()
	{
		$this->record->foo = 'bar';
		
		$this->assertTrue($this->record->isModified());
	}
	
	public function testHydrateResetsNew()
	{
		$this->record->hydrate(array('foo' => 'bar'));
		
		$this->assertFalse($this->record->isNew());
	}
	
	public function testHydrateResetsModified()
	{
		$this->record->foo = 'bar';
		
		$this->record->hydrate(array('foo' => 'baz'));
		
		$this->assertFalse($this->record->isModified());
	}
	
	public function testUnsetFlagsModified()
	{
		unset($this->record->foo);
		
		$this->assertFalse($this->record->isModified());
		
		$this->record->hydrate(array('foo' => 'foo', 'bar' => 'bar'));
		
		unset($this->record->foo);
		
		$this->assertTrue($this->record->isModified());
	}
	
	public function testDehydrate()
	{
		$this->record->foo = 'bar';
		
		$this->assertEquals(array('_id' => null, 'foo' => 'bar'), $this->record->dehydrate());
		
		$this->assertEquals($this->record->dehydrate(), $this->record->toArray());
	}
}

?>