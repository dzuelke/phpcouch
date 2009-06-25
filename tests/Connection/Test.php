<?php

class Connection_Test extends PHPUnit_Framework_TestCase
{
	public function testSetDatabase()
	{
		$con = new PhpcouchServerConnection(array());
		
		Phpcouch::registerConnection('foo', $con);
	}
	
	public function testGetDatabase()
	{
		$c = new PHPUnit_Framework_Constraint_IsInstanceOf('PhpcouchConnection');
		$this->assertThat(Phpcouch::getConnection('foo'), $c);
	}
}

?>