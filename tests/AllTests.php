<?php

use phpcouch\Phpcouch;

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
		
		require_once('PhpcouchTest.php');
		$suite->addTestSuite('PhpcouchTest');
		
		require_once('Configurable/AllTests.php');
		$suite->addTest(Configurable_AllTests::suite());
		
		require_once('Connection/AllTests.php');
		$suite->addTest(Connection_AllTests::suite());
		
		require_once('Record/AllTests.php');
		$suite->addTest(Record_AllTests::suite());
		
		return $suite;
	}
	
	public function setUp()
	{
		// set up and init PHPCouch
		require_once('../lib/phpcouch/Phpcouch.php');
		Phpcouch::bootstrap();
	}
	
	public function tearDown()
	{
	}
}

PHPUnit_Util_Filter::addDirectoryToWhitelist(realpath(dirname(__FILE__) . '/../lib/phpcouch'));
PHPUnit_Util_Filter::removeFileFromWhitelist(realpath(dirname(__FILE__) . '/../lib/phpcouch/Phpcouch.php'));
PHPUnit_Util_Filter::removeDirectoryFromWhitelist(realpath(dirname(__FILE__)));

if(PHPUnit_MAIN_METHOD == 'AllTests::main') {
	AllTests::main();
}

?>
