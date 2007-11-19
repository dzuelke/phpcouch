<?php

if(!defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}
 
require_once('PHPUnit/Framework.php');
require_once('PHPUnit/TextUI/TestRunner.php');
 
class AllTests extends PHPUnit_Framework_TestSuite
{
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite()
	{
		$suite = new AllTests('PHPCouch Test Suite');
		
		require_once('Phpcouch/AllTests.php');
		$suite->addTest(Phpcouch_AllTests::suite());
		
		require_once('Connection/AllTests.php');
		$suite->addTest(Connection_AllTests::suite());
		
		return $suite;
	}
	
	public function setUp()
	{
		// set up and init PHPCouch
		require_once('../lib/Phpcouch.class.php');
		Phpcouch::bootstrap();
	}
	
	public function tearDown()
	{
	}
}
 
if(PHPUnit_MAIN_METHOD == 'AllTests::main') {
	AllTests::main();
}

?>