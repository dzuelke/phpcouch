<?php

namespace phpcouch\connection;

use phpcouch\Exception;

/**
 * The main connection class, representing a connection registered with PHPCouch.
 *
 * @package    PHPCouch
 *
 * @author     David Zülke <david.zuelke@bitextender.com>
 * @copyright  Bitextender GmbH
 *
 * @since      1.0.0
 *
 * @version    $Id$
 */
class Connection extends \phpcouch\ConfigurableAbstract
{
	const COUCHDB_DEFAULT_PORT = 5984;
	
	const HTTP_DELETE = 'DELETE';
	const HTTP_GET = 'GET';
	const HTTP_POST = 'POST';
	const HTTP_PUT = 'PUT';
	
	const URL_PATTERN_ALLDBS = '%s_all_dbs';
	const URL_PATTERN_DATABASE = '%s%s/';
	const URL_PATTERRN_UUIDS = '%s_uuids?count=%d';
	const URL_PATTERN_CONFIG = '%s_config';
	const URL_PATTERN_STATS = '%s_stats';
	const URL_PATTERN_INFO = '%s';
	
	/**
	 * @var        PhpcouchIAdapter An adapter to use with this connection.
	 */
	protected $adapter = null;
	
	/**
	 * @var        string The base URL for this connection.
	 */
	public $baseUrl = '';
	
	/**
	 * The connection constructor.
	 *
	 * @param      string           A URI to the server, or null for the CouchDB defaults (http://localhost:5984/)
	 * @param      PhpcouchIAdapter The adapter to use with this connection, or null to use the default.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __construct($uri = null, \phpcouch\adapter\AdapterInterface $adapter = null)
	{
		if($uri !== null) {
			$info = @parse_url($uri);
			
			if($info === false) {
				throw new Exception(sprintf('Could not parse connection string "%s"', $uri));
			}
			
			if(count($info) == 1 && isset($info['path'])) {
				// special case: $uri was just "localhost" or so
				$info['host'] = $info['path'];
			}
		} else {
			// no info given. assume localhost
			$info['host'] = 'localhost';
		}
		
		// set some defaults if necessary
		if(!isset($info['scheme'])) {
			$info['scheme'] = 'http';
		}
		if(!isset($info['port'])) {
			$info['port'] = self::COUCHDB_DEFAULT_PORT;
		}
		// force path to / no matter what for now
		$info['path'] = '/';
		
		// TODO: user/pass, needs to be passed to adapter
		$info['auth'] = '';
		if(isset($info['user']) && isset($info["pass"])) {
			$info['auth'] = $info['user'] . ":" . $info["pass"] . "@";
		}
		
		if($adapter !== null) {
			$this->adapter = $adapter;
		} else {
			// no adapter given? let's create a default one.
			$this->adapter = new \phpcouch\adapter\PhpAdapter();
		}
		
		$this->baseUrl = sprintf('%s://%s%s:%s%s', $info['scheme'], $info['auth'], $info['host'], $info['port'], $info['path']);
	}
	
	/**
	 * Fetch the adapter used with this connection.
	 *
	 * @return     PhpcouchIAdapter The adapter instance.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	protected function getAdapter()
	{
		return $this->adapter;
	}
	
	/**
	 * Create a new database on the server.
	 *
	 * @param      string The name of the database to create.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function createDatabase($name)
	{
		if(!preg_match('#^[a-z][a-z0-9_$()+-/]*$#', $name)) {
			throw new \InvalidArgumentException('Invalid database name. Database names must conform to regular expression "^[a-z][a-z0-9_$()+-/]*$"');
		}
		
		try {
			$this->sendRequest(self::HTTP_PUT, sprintf('%s%s/', $this->baseUrl, rawurlencode($name)));
			
			try {
				return $this->retrieveDatabase($name);
			} catch(\Exception $e) {
				// something really, really messed up happened...
				// TODO: catch and throw appropriate exceptions
				throw new \Exception($e->getMessage(), $e->getCode(), $e);
			}
		} catch(\Exception $e) {
			// TODO: catch and throw appropriate exceptions
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		} catch(\Exception $e) {
			// TODO: catch and throw appropriate exceptions
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	/**
	 * Retrieve database information from the server.
	 *
	 * @param      string The database name.
	 *
	 * @return     PhpcouchDatabase The database instance.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function retrieveDatabase($name)
	{
		// TODO: catch exceptions
		$result = $this->sendRequest(self::HTTP_PUT, sprintf(self::URL_PATTERN_DATABASE, $this->baseUrl, rawurlencode($name)));
		
		$database = new \phpcouch\record\Database($this);
		$database->hydrate($result);
		
		return $database;
	}
	
	/**
	 * Delete a database from the server.
	 *
	 * @param      string The name of the database to delete.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function deleteDatabase($name)
	{
		// TODO: catch exceptions
		// TODO: hydrate to Record
		return $this->sendRequest(self::HTTP_DELETE, sprintf(self::URL_PATTERN_DATABASE, $this->baseUrl, rawurlencode($name)));
	}
	
	public function retrieveUuids($count = 10)
	{
		// TODO: catch exceptions
		// TODO: hydrate to Record?
		return $this->sendRequest(self::HTTP_GET, sprintf(self::URL_PATTERN_UUIDS, $this->baseUrl, $count));
	}
	
	public function retrieveInfo()
	{
		// TODO: catch exceptions
		// TODO: hydrate to Record
		return $this->sendRequest(self::HTTP_GET, sprintf(self::URL_PATTERN_INFO, $this->baseUrl));
	}
	
	public function retrieveConfig()
	{
		// TODO: catch exceptions
		// TODO: hydrate to Record
		return $this->sendRequest(self::HTTP_GET, sprintf(self::URL_PATTERN_CONFIG, $this->baseUrl));
	}
	
	public function retrieveStats()
	{
		// TODO: catch exceptions
		// TODO: hydrate to Record
		return $this->sendRequest(self::HTTP_GET, sprintf(self::URL_PATTERN_STATS, $this->baseUrl));
	}
	
	/**
	 * List all databases on the server.
	 *
	 * @return     array An array of database names.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function listDatabases()
	{
		// special case: __all_dbs is simply an array, not a struct
		// thus we also return a simple array of names here
		// TODO: catch exceptions
		return $this->sendRequest(self::HTTP_GET, sprintf(self::URL_PATTERN_ALLDBS, $this->baseUrl));
	}
	
	public function sendRequest($method, $resource, $headers = array(), $payload = null)
	{
		try {
			return $this->getAdapter()->sendRequest($method, $resource, $headers, $payload);
		} catch(\Exception $e) {
			// TODO: catch and throw appropriate exceptions
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}
	}
}

?>
