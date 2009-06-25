<?php

class Configurable_AllTests extends PHPUnit_Framework_TestSuite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('PHPCouch Configurable Test Suite');
		
		require_once('Configurable/Test.php');
		$suite->addTestSuite('Configurable_Test');
		
		return $suite;
	}
}

?>