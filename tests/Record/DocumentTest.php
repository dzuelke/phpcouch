<?php

class Document_Test extends PHPUnit_Framework_TestCase
{
	
	protected $document;

	public function setUp()
	{
		require_once('lib/Connection/Dummy.class.php');
		require_once('lib/Record/Document.class.php');
		require_once('lib/Record/Database.class.php');
	
		$this->document = new TestPhpcouchDocument(new TestDatabase(new TestPhpcouchDummyConnection()));
	
	}
	
	public function testRetrieveAttachment() 
	{
	
	}
	
	public function testSaveCreateDocument()
	{
	
	}

	public function testSaveUpdateDocument()
	{
	
	}

}

?>
