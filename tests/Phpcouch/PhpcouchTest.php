<?php

use phpcouch\Phpcouch;
use phpcouch\Exception;
use phpcouch\connection;
use phpcouch\adapter;

/**
 * Phpcouch Test
 * 
 * @package    PHPCouch
 * @subpackage Tests
 * 
 * @author     Simon Thulbourn <simon+github@thulbourn.com>
 * @copyright  authors
 * 
 * @since      1.0.0
 */
class PhpcouchTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Tests bootstrap loading
	 * 
	 * @assert     equals
	 * 
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testBootstrap()
	{
		Phpcouch::bootstrap();
		
		$autoloads = spl_autoload_functions();
		
		$phpcouchAutoload = array('phpcouch\Phpcouch', 'autoload');
		
		foreach ($autoloads as $callback) {
			if ($callback === $phpcouchAutoload) {
				$this->assertEquals($callback, $phpcouchAutoload);
			}
		}
	}
	
	/**
	 * Tests autoload
	 * 
	 * @assert     true
	 * 
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testAutoload()
	{
		Phpcouch::bootstrap();
		
		// use full class path to make sure it's using ours and not the one from pecl_http
		$class = new \phpcouch\http\HttpRequest();
		
		$this->assertTrue(class_exists('\phpcouch\http\HttpRequest'));
	}
	
	/**
	 * Test version info
	 * 
	 * @assert     equals
	 * 
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testVersionInfo()
	{
		$version = \phpcouch\VERSION_NUMBER;
		
		// only append a status (like "RC3") if it is set
		if(\phpcouch\VERSION_STATUS !== null) {
			$version .= '-' . \phpcouch\VERSION_STATUS;
		}
		
		$this->assertEquals(Phpcouch::getVersionInfo(), $version);
	}
	
	/**
	 * Test version string
	 * 
	 * @assert     equals
	 * 
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testVersionString()
	{
		$version = \phpcouch\VERSION_NUMBER;
		
		// only append a status (like "RC3") if it is set
		if(\phpcouch\VERSION_STATUS !== null) {
			$version .= '-' . \phpcouch\VERSION_STATUS;
		}
		
		$this->assertEquals(Phpcouch::getVersionString(), 'PHPCouch/' . $version);
	}
	
	/**
	 * Tests register connection
	 * 
	 * @assert     same
	 * 
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testRegisterConnection()
	{
		$connection = $this->getMock('\phpcouch\connection\connection');
		
		PhpCouch::registerConnection('test', $connection);
		
		$this->assertSame(PhpCouch::getConnection('test'), $connection);
	}
	
	/**
	 * Tests register connection
	 * 
	 * @assert     same
	 * 
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testUnregisterConnection()
	{
		$connection = $this->getMock('\phpcouch\connection\connection');
		
		PhpCouch::registerConnection('test', $connection);
		
		$this->assertSame(PhpCouch::unregisterConnection('test'), $connection);
	}
}

?>