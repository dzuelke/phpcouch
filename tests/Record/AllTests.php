<?php

class Record_AllTests extends PHPUnit_Framework_TestSuite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('PHPCouch Record Test Suite');
		
		require_once('Record/Test.php');
		$suite->addTestSuite('Record_Test');
		
		require_once('Record/MutableTest.php');
		$suite->addTestSuite('Record_MutableTest');
		
		require_once('Record/DocumentTest.php');
		$suite->addTestSuite('Document_Test');
		
		require_once('Record/DatabaseTest.php');
		$suite->addTestSuite('Database_Test');

		return $suite;
	}
}

?>
