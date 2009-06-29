<?php

use phpcouch\Phpcouch;
use phpcouch\connection\Connection;

class PhpcouchTest extends PHPUnit_Framework_TestCase
{
	public function setExpectedException($exceptionName, $autoload = true)
	{
		if($autoload) {
			class_exists($exceptionName);
		}
		
		parent::setExpectedException($exceptionName);
	}
	
	public function tearDown()
	{
		Phpcouch::clearConnections();
	}

	public function testGetVersionInfo()
	{
		$versionNumber = \phpcouch\VERSION_NUMBER;
		$versionStatus = \phpcouch\VERSION_STATUS;
		
		$this->assertContains($versionNumber, Phpcouch::getVersionInfo());
		
		$expected = $versionNumber . ($versionStatus ? '-' . $versionStatus : '');
		
		$this->assertEquals($expected, Phpcouch::getVersionInfo());
	}
	
	public function testGetVersionString()
	{
		$this->assertEquals('PHPCouch/' . Phpcouch::getVersionInfo(), Phpcouch::getVersionString());
	}
	
	public function testGetDefaultConnectionThrowsException()
	{
		$this->setExpectedException('phpcouch\Exception');
		
		Phpcouch::getConnection();
	}
	
	public function testGetNamedConnectionThrowsException()
	{
		$this->setExpectedException('phpcouch\Exception');
		
		Phpcouch::getConnection('zomg');
	}
	
	public function testRegisterConnection()
	{
	
		$con1 = new Connection(array());
		Phpcouch::registerConnection('foo', $con1);
		
		$this->assertSame($con1, Phpcouch::getConnection('foo'));
		
		$con2 = new Connection(array());
		Phpcouch::registerConnection('foo', $con2);
		
		$this->assertNotSame($con1, Phpcouch::getConnection('foo'));
		
		$c = new PHPUnit_Framework_Constraint_IsInstanceOf('phpcouch\connection\Connection');
		$this->assertThat(Phpcouch::getConnection('foo'), $c);
	}
	
	public function testUnregisterConnection()
	{
		Phpcouch::registerConnection('foo', new Connection(array()));
		
		$con = Phpcouch::getConnection('foo');
		
		$ret = Phpcouch::unregisterConnection('foo');
		
		$this->assertNotNull($ret);
		
		$c = new PHPUnit_Framework_Constraint_IsInstanceOf('phpcouch\connection\Connection');
		$this->assertThat($ret, $c);
		
		$this->setExpectedException('phpcouch\Exception');
		
		Phpcouch::getConnection('foo');
	}
	
	public function testUnregisterConnectionResetsDefault()
	{

		$con1 = new Connection(array());
		Phpcouch::registerConnection('foo', $con1);
		
		$this->assertSame($con1, Phpcouch::getConnection('foo'));
		
		Phpcouch::unregisterConnection('foo');
		
		$this->setExpectedException('phpcouch\Exception');
		Phpcouch::getConnection();
	}
	
	public function testDefaultConnections()
	{

		Phpcouch::registerConnection('foo', new Connection(array()));
		
		$this->assertSame(Phpcouch::getConnection(), Phpcouch::getConnection('foo'));
		
		$con1 = new Connection(array());
		Phpcouch::registerConnection('bar', $con1);
		
		$this->assertSame(Phpcouch::getConnection(), Phpcouch::getConnection('bar'));
		$this->assertNotSame(Phpcouch::getConnection(), Phpcouch::getConnection('foo'));
		
		$con2 = new Connection(array());
		Phpcouch::registerConnection('baz', $con2, false);
		
		$this->assertSame(Phpcouch::getConnection(), Phpcouch::getConnection('bar'));
		$this->assertNotSame(Phpcouch::getConnection(), Phpcouch::getConnection('baz'));
		
		Phpcouch::registerConnection('bar', $con1, false);
		
		// bar was registered, not as default, but it was previously the default!
		$this->assertSame(Phpcouch::getConnection(), Phpcouch::getConnection('bar'));
		
		// remove bar, now we do not have a default
		Phpcouch::unregisterConnection('bar');
		
		$this->setExpectedException('phpcouch\Exception');
		Phpcouch::getConnection();
	}
}

?>
