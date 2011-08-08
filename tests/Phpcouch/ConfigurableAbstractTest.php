<?php

use phpcouch\Phpcouch;
use phpcouch\Exception;
use phpcouch\connection;
use phpcouch\adapter;

/**
 * Configurable Abstract Test
 *
 * @package    PHPCouch
 * @subpackage Tests
 *
 * @author     Simon Thulbourn <simon+github@thulbourn.com>
 * @copyright  authors
 *
 * @since      1.0.0
 */
class ConfigurableAbstractTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup
	 */
	public function setUp()
	{
		Phpcouch::bootstrap();
	}
	
	/**
	 * Test set option
	 *
	 * @assert       true
	 * @assert       equals
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testSetOption()
	{
		$confAbstract = $this->getMockForAbstractClass('phpcouch\ConfigurableAbstract');
		
		$confAbstract->setOption('foo', 'bar');
		
		// test normal usage
		$this->assertTrue($confAbstract->hasOption('foo'));
		$this->assertEquals($confAbstract->getOption('foo'), 'bar');
		
		// test don't overwrite
		$confAbstract->setOption('foo', 'baz', false);
		$this->assertEquals($confAbstract->getOption('foo'), 'bar');
		
		// test overwrite
		$confAbstract->setOption('foo', 'baz', true);
		$this->assertEquals($confAbstract->getOption('foo'), 'baz');
	}
	
	/**
	 * Test get option
	 *
	 * @assert       equals
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testGetOption()
	{
			$confAbstract = $this->getMockForAbstractClass('phpcouch\ConfigurableAbstract');
			
			// test default
			$this->assertEquals($confAbstract->getOption('foo', 'bar'), 'bar');
			
			// null
			$this->assertEquals($confAbstract->getOption('foo'), null);
			
			$confAbstract->setOption('foo', 'bar');
			$this->assertEquals($confAbstract->getOption('foo'), 'bar');
	}
	
	/**
	 * Test has option
	 *
	 * @assert       true
	 * @assert       false
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testHasOption()
	{
		$confAbstract = $this->getMockForAbstractClass('phpcouch\ConfigurableAbstract');
		
		$this->assertFalse($confAbstract->hasOption('foo'));
		
		$confAbstract->setOption('foo', 'bar');
		$this->assertTrue($confAbstract->hasOption('foo'));
	}
	
	/**
	 * Test remove option
	 *
	 * @assert       false
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testRemoveOption()
	{
		$confAbstract = $this->getMockForAbstractClass('phpcouch\ConfigurableAbstract');
		
		$confAbstract->setOption('foo', 'bar');
		$confAbstract->removeOption('foo');
		
		$this->assertFalse($confAbstract->hasOption('foo'));
	}
	
	/**
	 * Test set options
	 *
	 * @assert       equals
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testSetOptions()
	{
		$data = array(
			'foo' => 'bar',
			'baz' => 'buzz'
		);
		
		$confAbstract = $this->getMockForAbstractClass('phpcouch\ConfigurableAbstract');
		
		$confAbstract->setOptions($data);
		
		$this->assertEquals($confAbstract->getOptions(), $data);
	}
	
	/**
	 * Test clear options
	 *
	 * @assert       equals
	 *
	 * @author       Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testClearOptions()
	{
		$data = array(
			'foo' => 'bar',
			'baz' => 'buzz'
		);
		
		$confAbstract = $this->getMockForAbstractClass('phpcouch\ConfigurableAbstract');
		
		$confAbstract->setOptions($data);
		$confAbstract->clearOptions();
		
		$this->assertEquals($confAbstract->getOptions(), array());
	}
}

?>