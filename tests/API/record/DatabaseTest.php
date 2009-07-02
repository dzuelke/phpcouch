<?php

use phpcouch\Phpcouch;
use phpcouch\Exception;
use phpcouch\connection;
use phpcouch\record;
use phpcouch\adapter;

define('RESPONSE_PATH', dirname(__FILE__) .'/_files');

require_once('PHPUnit/Framework.php');
require_once('PHPUnit/TextUI/TestRunner.php');

require('../../../lib/phpcouch/Phpcouch.php');
require_once('Zend/Loader/Autoloader.php');

class DatabaseTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		Phpcouch::bootstrap();
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); 
		$autoloader->setFallbackAutoloader(true);
	}
		
	public function testNewDocument()
	{
		PhpCouch::registerConnection('default', $con = new connection\Connection(null, new adapter\ZendhttpclientAdapter()));
		
		$adapter = new Zend_Http_Client_Adapter_Test();
		$con->getAdapter()->getClient()->setAdapter($adapter);
				
		$response = file_get_contents(RESPONSE_PATH .'/retrieveDatabase');
		$adapter->setResponse($response);
		
		$db = $con->retrieveDatabase('foo');
		
		// This is retarded, but I need to think of a better test.
		$this->assertEquals($db->newDocument(), $db->newDocument());
	}

	public function testRetrieveDocument()
	{
		PhpCouch::registerConnection('default', $con = new connection\Connection(null, new adapter\ZendhttpclientAdapter()));

		$adapter = new Zend_Http_Client_Adapter_Test();
		$con->getAdapter()->getClient()->setAdapter($adapter);

		$response = file_get_contents(RESPONSE_PATH .'/retrieveDatabase');
		$adapter->setResponse($response);
		
		$db = $con->retrieveDatabase('foo');

		$response = file_get_contents(RESPONSE_PATH .'/retrieveDocument');
		$adapter->setResponse($response);
		
		$doc = $db->retrieveDocument('foo');
		
		$result = $con->getAdapter()->get($con->buildUri('foo/foo'));
		$doc = $db->newDocument();
		$doc->hydrate($result);
		
		$this->assertEquals($doc, $db->retrieveDocument('foo'));
	}
	
	/*
	
		This test cannot be done yet.
	
	public function testRetrieveAttachment()
	{
		PhpCouch::registerConnection('default', $con = new connection\Connection(null, new adapter\ZendhttpclientAdapter()));

		$adapter = new Zend_Http_Client_Adapter_Test();
		$con->getAdapter()->getClient()->setAdapter($adapter);

		$response = file_get_contents(RESPONSE_PATH .'/retrieveDatabase');
		$adapter->setResponse($response);
		
		$db = $con->retrieveDatabase('foo');

		$response = file_get_contents(RESPONSE_PATH .'/retrieveAttachment');
		$adapter->setResponse($response);
		
		//$uri = $con->getAdapter()->get($con->buildUri('foo/foo'));
		
		$this->assertEquals($url, $db->retrieveAttachment('test.php', 'foo'));
	}*/
		
}

?>