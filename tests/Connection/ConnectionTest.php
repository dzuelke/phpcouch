<?php

require_once('PHPUnit/Framework.php');

class Connection_ConnectionTest extends PHPUnit_Framework_TestCase
{
	public function testSetDatabase()
	{
		$con = new PhpcouchConnection(array());
		
		Phpcouch::registerConnection('foo', $con);
	}
	
	public function testGetDatabase()
	{
		$c = new PHPUnit_Framework_Constraint_IsInstanceOf('PhpcouchConnection');
		$this->assertThat(Phpcouch::getConnection('foo'), $c);
	}
}

?>