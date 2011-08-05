<?php

use phpcouch\Phpcouch;
use phpcouch\Exception;
use phpcouch\connection;
use phpcouch\adapter;

define('RESPONSE_PATH', dirname(__FILE__) .'/_files');

require_once('PHPUnit/Framework.php');
require_once('PHPUnit/TextUI/TestRunner.php');

require('../../../lib/phpcouch/Phpcouch.php');
require_once('Zend/Loader/Autoloader.php');
 
class PhpcouchConnectionTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		Phpcouch::bootstrap();
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); 
		$autoloader->setFallbackAutoloader(true);
	}
	
	public function testConnection()
	{
		PhpCouch::registerConnection('default', $con = new connection\Connection(null, new adapter\ZendhttpclientAdapter()));
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$con->getAdapter()->getClient()->setAdapter($adapter);
	}
	
	public function testListDatabase()
	{
		PhpCouch::registerConnection('default', $con = new connection\Connection(null, new adapter\ZendhttpclientAdapter()));
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$con->getAdapter()->getClient()->setAdapter($adapter);
		
		$response = file_get_contents(RESPONSE_PATH .'/listDatabases');
		
		$adapter->setResponse($response);
				
		$this->assertEquals(array('foo'), $con->listDatabases());
	}
	
	public function testRetrieveDatabase()
	{
		PhpCouch::registerConnection('default', $con = new connection\Connection(null, new adapter\ZendhttpclientAdapter()));
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$con->getAdapter()->getClient()->setAdapter($adapter);
		
		$response = file_get_contents(RESPONSE_PATH .'/retrieveDatabase');
		
		$adapter->setResponse($response);
				
		$result = $con->getAdapter()->get($con->buildUri('foo'));
		$database = new \phpcouch\record\Database($con);
		$database->hydrate($result);
					
		$this->assertEquals($database, $con->retrieveDatabase('foo'));
	}
	
	public function testCreateDatabase()
	{
		PhpCouch::registerConnection('default', $con = new connection\Connection(null, new adapter\ZendhttpclientAdapter()));
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$con->getAdapter()->getClient()->setAdapter($adapter);
		
		$response = file_get_contents(RESPONSE_PATH .'/createDatabase');
		
		$adapter->setResponse($response);
				
		$result = $con->getAdapter()->get($con->buildUri('foo'));
		$database = new \phpcouch\record\Database($con);
		$database->hydrate($result);
					
		$this->assertEquals($database, $con->createDatabase('foo'));
	}

	public function testDeleteDatabase()
	{
		PhpCouch::registerConnection('default', $con = new connection\Connection(null, new adapter\ZendhttpclientAdapter()));
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$con->getAdapter()->getClient()->setAdapter($adapter);
		
		$response = file_get_contents(RESPONSE_PATH .'/deleteDatabase');
		
		$adapter->setResponse($response);
		
		$obj = new stdClass();
		$obj->ok = true;

		$this->assertEquals($obj, $con->deleteDatabase('foo'));
	}
}

?>