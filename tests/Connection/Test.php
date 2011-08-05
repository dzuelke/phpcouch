<?php

use phpcouch\Phpcouch;

class Connection_Test extends PHPUnit_Framework_TestCase
{
	public function testSetDatabase()
	{
		$con = new \phpcouch\connection\Connection(array());
		
		Phpcouch::registerConnection('foo', $con);
	}
	
	public function testGetDatabase()
	{
		$c = new PHPUnit_Framework_Constraint_IsInstanceOf('phpcouch\connection\Connection');
		$this->assertThat(Phpcouch::getConnection('foo'), $c);
	}
}

?>
