<?php

class Configurable_Test extends PHPUnit_Framework_TestCase
{
	protected $configurable;
	
	public function setUp()
	{
		require_once('lib/TestPhpcouchConfigurable.class.php');
		
		$this->configurable = new TestPhpcouchConfigurable();
	}
	
	public function testInitiallyEmpty()
	{
		$this->assertEquals(array(), $this->configurable->getOptions());
	}
	
	public function testGetSetOption()
	{
		$this->configurable->setOption('foo', 'bar');
		
		$this->assertEquals('bar', $this->configurable->getOption('foo'));
	}
	
	public function testHasOption()
	{
		$this->assertFalse($this->configurable->hasOption('foo'));
		
		$this->configurable->setOption('foo', 'bar');
		
		$this->assertTrue($this->configurable->hasOption('foo'));
	}
	
	public function testGetDefaultValue()
	{
		$this->assertEquals('lolz', $this->configurable->getOption('zomg', 'lolz'));
	}
	
	public function testSetOptionOverwrite()
	{
		$this->assertTrue($this->configurable->setOption('foo', 'bar', false));
		
		$this->assertEquals('bar', $this->configurable->getOption('foo'));
		
		$this->assertFalse($this->configurable->setOption('foo', 'baz', false));
		
		$this->assertNotEquals('baz', $this->configurable->getOption('foo'));
		$this->assertEquals('bar', $this->configurable->getOption('foo'));
		
		$this->assertTrue($this->configurable->setOption('foo', 'baz'));
		
		$this->assertEquals('baz', $this->configurable->getOption('foo'));
	}
	
	public function testSetOptions()
	{
		$this->configurable->setOptions(array('foo' => 'foo', 'bar' => 'bar'));
		$this->assertEquals(array('foo' => 'foo', 'bar' => 'bar'), $this->configurable->getOptions());
		
		$this->configurable->setOptions(array('bar' => 'baz', 'zomg' => 'lol'));
		$this->assertEquals(array('foo' => 'foo', 'bar' => 'baz', 'zomg' => 'lol'), $this->configurable->getOptions());
	}
	
	public function testRemoveOption()
	{
		$this->configurable->setOptions(array('foo' => 'foo', 'bar' => 'bar'));
		
		$this->assertTrue($this->configurable->removeOption('bar'));
		$this->assertFalse($this->configurable->removeOption('baz'));
		$this->assertNull($this->configurable->getOption('bar'));
		
		$this->assertEquals(array('foo' => 'foo'), $this->configurable->getOptions());
	}
	
	public function testClearOptions()
	{
		$this->configurable->setOptions(array('foo' => 'foo', 'bar' => 'bar'));
		
		$this->configurable->clearOptions();
		
		$this->assertEquals(array(), $this->configurable->getOptions());
	}
}

?>