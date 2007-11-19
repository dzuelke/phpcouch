<?php

require_once('PHPUnit/Framework.php');

class Phpcouch_DatabaseTest extends PHPUnit_Framework_TestCase
{
	public function testInitiallyEmpty()
	{
		$this->assertEquals(array(), $this->configurable->getOptions());
	}
	
	public function testSetOption()
	{
		$this->configurable->setOption('foo', 'bar');
		
		$this->assertEquals('bar', $this->configurable->getOption('foo'));
	}
	
	public function testGetDefaultValue()
	{
		$this->assertEquals('lolz', $this->configurable->getOption('zomg', 'lolz'));
	}
	
	public function testSetOptionOverwrite()
	{
		$this->configurable->setOption('foo', 'bar', false);
		
		$this->assertEquals('bar', $this->configurable->getOption('foo'));
		
		$this->configurable->setOption('foo', 'baz', false);
		
		$this->assertNotEquals('baz', $this->configurable->getOption('foo'));
		$this->assertEquals('bar', $this->configurable->getOption('foo'));
		
		$this->configurable->setOption('foo', 'baz');
		
		$this->assertEquals('baz', $this->configurable->getOption('foo'));
	}
}

?>