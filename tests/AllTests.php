<?php

if(!defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}
 
require_once('PHPUnit/Framework.php');
require_once('PHPUnit/TextUI/TestRunner.php');
 
class AllTests
{
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('PHPCouch Test Suite');
		
		require_once('Connection/AllTests.php');
		$suite->addTest(Connection_AllTests::suite());
		
		return $suite;
	}
}
 
if(PHPUnit_MAIN_METHOD == 'AllTests::main') {
	AllTests::main();
}

?>