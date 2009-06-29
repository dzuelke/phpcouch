<?php

class Database_Test extends PHPUnit_Framework_TestCase
{
	
	protected $database;

	public function setUp()
	{
		require_once('lib/Connection/Dummy.class.php');
		require_once('lib/Record/Database.class.php');
	
		$this->document = new TestDatabase(new TestPhpcouchDummyConnection("http://admin:neverland@127.0.0.1:5984/"));
	
	}
	
	

}

?>
