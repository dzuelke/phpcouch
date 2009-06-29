<?php

class Document_Test extends PHPUnit_Framework_TestCase
{
	
	protected $document;

	public function setUp()
	{
		require_once('lib/Connection/Dummy.class.php');
		require_once('lib/Record/Document.class.php');
		require_once('lib/Record/Database.class.php');
	
		$this->document = new TestPhpcouchDocument(new TestDatabase(new TestPhpcouchDummyConnection("http://admin:neverland@127.0.0.1:5984/")));
	
	}
	
	public function provider()
	{
		return array(
		  array("", "", ""),
		  array("", "", "")
		);
	}
	
	/**
	 * @dataProvider provider
	 */
	public function testRetrieveAttachment($docid, $attid, $data) 
	{
		$connection = $this->document->getConnection();
		$database = $this->document->getDatabase();

		$dat2 = $connection->createDatabase("test/database");
		
		//temp until uri building is implemented
		$uri = $connection->baseUrl . rawurlencode($database->getName()) . '/' . rawurlencode($document->_id) . '/' . rawurlencode($attid);

		//test with $uri and $data

		$connection->deleteDatabase($dat2->getName());

		$this->markIncomplete('');
	}
	
	public function testHydrateGetHasAttachments()
	{
		$attach = array(
				"DSCF0114.JPG" => array(),
				"sdfs" => array()
		);
		$data = array(
			"_id" => "somedoc", 
			"_rev" => "12341234132", 
			"_attachments" => $attach
		);
		$this->document->hydrate($data);
		
		$this->assertEquals($attach, $this->document->getAttachments());
		$this->assertEquals(count($attach), $this->document->hasAttachments());
		
		$this->document->dehydrate();
	}
	
	public function testSaveCreateDocument()
	{
		
		$database = $this->getMock('TestDatabase');
		$this->document->database = $database;
		$this->document->sdfsdf = "sdfds";
		
				
		$database->expects($this->once())
			->method('createDocument')
			->with($this->equalTo($this->document));
						
		$this->document->save();
			
	}

	public function testSaveUpdateDocument()
	{
		$database = $this->getMock('TestDatabase');
		$this->document->database = $database;

		$this->document->sdfsdf = "sdfds";
		$this->document->save();

		$this->document->sdfsfdsfd = "sdfsdfs";		
				
		$database->expects($this->once())
			->method('createDocument')
			->with($this->equalTo($this->document));
						
		$this->document->save();
	
	}

}

?>
