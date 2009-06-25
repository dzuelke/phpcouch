<?php

class Connection_AllTests extends PHPUnit_Framework_TestSuite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('PHPCouch Connection Test Suite');
		
		require_once('Connection/Test.php');
		$suite->addTestSuite('Connection_Test');
		
		return $suite;
	}
}

?>