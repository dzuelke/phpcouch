<?php
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
class DatabaseTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		\phpcouch\Phpcouch::bootstrap();
	}
	/**
	 * Placeholder test, keep until actual tests are written
	 * 
	 * @assert     true
	 * 
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function testPlaceholder()
	{
		$this->assertTrue(true);
	}
	
	
	
	public function testSearchCouchdbLucene()
	{
		$this->markTestIncomplete();
		$resp = new \phpcouch\http\HttpResponse();
		$resp->setContent('{"limit":25,"etag":"1250dea1818b6af0","fetch_duration":5,"q":"default:p*","search_duration":0,"total_rows":3,"skip":0,"rows":[{"id":"0fb46f03e4bdc52696a17717e207361b","doc":{"_rev":"1-b85e73dd25dfeb969e1b490a39e9d00b","_id":"0fb46f03e4bdc52696a17717e207361b","name":"Product 1","type":"product","product_number":1001},"score":1},{"id":"0fb46f03e4bdc52696a17717e2074212","doc":{"_rev":"1-7a78fead06bc9c307b1d675b2aec5642","_id":"0fb46f03e4bdc52696a17717e2074212","name":"Product 2","type":"product","product_number":1002},"score":1},{"id":"0fb46f03e4bdc52696a17717e2074ea7","doc":{"_rev":"1-8828564cd07a06d0ed1ea5cc970b141b","_id":"0fb46f03e4bdc52696a17717e2074ea7","name":"Product 3","type":"product","product_number":1003},"score":1}]}');
		$con = $this->getMock('phpcouch\connection\Connection');
		$con->expects($this->once())->method('buildUrl')->will($this->returnValue('http://localhost:5984/_fti/local/testdb/_design/testdesigndoc/products?q=p*'));
		$con->expects($this->once())->method('sendRequest')->will($this->returnValue($resp));
		$db = new \phpcouch\record\Database($con);
		$db->db_name = 'testdb';
		
		$results = $db->searchCouchdbLucene('local', 'testdesigndoc', 'products', 'p*', array('include_docs' => true));
		$this->assertEquals(3, $results->getTotalRows());
		$rows = $results->getRows();
		$row = $rows[1];
		$this->assertEquals('Product 2', $row->getDocument()->name);
	}
}

?>