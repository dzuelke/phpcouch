<?php

class Document_Test extends PHPUnit_Framework_TestCase
{
	
	protected $document;

	public function setUp()
	{
		require_once('lib/Connection/Dummy.class.php');
		require_once('lib/Record/Document.class.php');
	
		$this->document = new TestPhpcouchDocument(new TestPhpcouchDummyConnection(array()));
	
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
