<?php

if(!defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD', 'Framework_AllTests::main');
}
 
require_once('PHPUnit/Framework.php');
require_once('PHPUnit/TextUI/TestRunner.php');
 
class Connection_AllTests
{
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('PHPCouch Connection Test Suite');
		
		require_once('Connection/ConnectionTest.php');
		$suite->addTestSuite('Connection_ConnectionTest');
		
		return $suite;
	}
}
 
if(PHPUnit_MAIN_METHOD == 'Framework_AllTests::main') {
	Framework_AllTests::main();
}

?>