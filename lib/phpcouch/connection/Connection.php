<?php

namespace phpcouch\connection;

use phpcouch\Exception;
use phpcouch\http\HttpRequest, phpcouch\http\HttpResponse;

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
	
	const URL_PATTERN_ALLDBS = '%s_all_dbs';
	const URL_PATTERN_DATABASE = '%s%s/';
	const URL_PATTERN_UUIDS = '%s_uuids';
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
	
	public function buildUrl($template, array $values = array(), $options = array())
	{
		$url = vsprintf($template, array_merge(array($this->baseUrl), array_map('rawurlencode', $values)));
		
		if($options) {
			$url .= '?' . http_build_query($options);
		}
		
		return $url;
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
			$request = new HttpRequest($this->buildUrl(self::URL_PATTERN_DATABASE, array($name)));
			$result = $this->sendRequest($request);
			
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
		$request = new HttpRequest($this->buildUrl(self::URL_PATTERN_DATABASE, array($name)));
		// TODO: catch exceptions
		// TODO: hydrate to Record
		$result = $this->sendRequest($request);
		
		$database = new \phpcouch\record\Database($this);
		$database->hydrate(json_decode($result->getContent()));
		
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
		$request = new HttpRequest($this->buildUrl(self::URL_PATTERN_DATABASE, array($name)));
		// TODO: catch exceptions
		// TODO: hydrate to Record
		return $this->sendRequest($request);
	}
	
	public function retrieveUuids($count = 1)
	{
		// TODO: catch exceptions
		$record = new \phpcouch\record\Record($this);
		$record->hydrate(json_decode($this->sendRequest(new HttpRequest($this->buildUrl(self::URL_PATTERN_UUIDS, array(), array('count' => $count))))->getContent()));
		return $record;
	}
	
	public function retrieveInfo()
	{
		// TODO: catch exceptions
		$database = new \phpcouch\record\Database($this);
		$database->hydrate(json_decode($this->sendRequest(new HttpRequest($this->buildUrl(self::URL_PATTERN_INFO)))->getContent()));
		return $database;
	}
	
	public function retrieveConfig()
	{
		// TODO: catch exceptions
		$record = new \phpcouch\record\Record($this);
		$record->hydrate(json_decode($this->sendRequest(new HttpRequest($this->buildUrl(self::URL_PATTERN_CONFIG)))->getContent()));
		return $record;
	}
	
	public function retrieveStats()
	{
		// TODO: catch exceptions
		$record = new \phpcouch\record\Record($this);
		$record->hydrate(json_decode($this->sendRequest(new HttpRequest($this->buildUrl(self::URL_PATTERN_STATS)))->getContent()));
		return $record;
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
		// TODO: catch exceptions
		// special case: _all_dbs is simply an array, not a struct
		// thus we also return a simple array of values here
		return json_decode($this->sendRequest(new HttpRequest($this->buildUrl(self::URL_PATTERN_ALLDBS)))->getContent());
	}
	
	public function sendRequest(\phpcouch\http\HttpRequest $request)
	{
		// TODO: should we wrap exceptions here? I'm not sure really.
		return $this->getAdapter()->sendRequest($request);
	}
}

?>
