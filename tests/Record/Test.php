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
		
		$this->record->foo = 'bar';
		
		$this->assertEquals('bar', $this->record->foo);
		
		$this->assertTrue(isset($this->record->foo));
		$this->assertFalse(isset($this->record->bar));
		
		unset($this->record->foo);
		
		$this->assertFalse(isset($this->record->foo));
	}
}

?>