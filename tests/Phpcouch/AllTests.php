<?php

if(!defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD', 'Framework_AllTests::main');
}
 
require_once('PHPUnit/Framework.php');
require_once('PHPUnit/TextUI/TestRunner.php');
 
class Phpcouch_AllTests extends PHPUnit_Framework_TestSuite
{
	public static function main()
	{
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}
	
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('PHPCouch Main Test Suite');
		
		require_once('Phpcouch/PhpcouchTest.php');
		$suite->addTestSuite('Phpcouch_PhpcouchTest');
		
		require_once('Phpcouch/ConfigurableTest.php');
		$suite->addTestSuite('Phpcouch_ConfigurableTest');
		
		require_once('Phpcouch/RecordTest.php');
		$suite->addTestSuite('Phpcouch_RecordTest');
		
		return $suite;
	}
}
 
if(PHPUnit_MAIN_METHOD == 'Framework_AllTests::main') {
	Framework_AllTests::main();
}

?>