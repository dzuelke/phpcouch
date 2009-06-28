<?php

namespace phpcouch\connection;

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
	
	/**
	 * @var        PhpcouchIAdapter An adapter to use with this connection.
	 */
	protected $adapter = null;
	
	/**
	 * @var        string The base URL for this connection.
	 */
	protected $baseUrl = '';
	
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
		
		if($adapter !== null) {
			$this->adapter = $adapter;
		} else {
			// no adapter given? let's create a default one.
			$this->adapter = new \phpcouch\adapter\Php();
		}
		
		$this->baseUrl = sprintf('%s://%s:%s%s', $info['scheme'], $info['host'], $info['port'], $info['path']);
	}
	
	/**
	 * Build a URI from the given information.
	 *
	 * @param      string The ID of the entity to fetch.
	 * @param      array  An array of additional arguments to set in the URL.
	 *
	 * @return     string A generated URL.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function buildUri($id = null, array $arguments = array())
	{
		return sprintf('%s%s?%s',
			$this->baseUrl,
			rawurlencode($id),
			http_build_query($arguments)
		);
	}
	
	/**
	 * Fetch the adapter used with this connection.
	 *
	 * @return     PhpcouchIAdapter The adapter instance.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getAdapter()
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
		// result doesn't matter here
		$this->adapter->put($this->buildUri($name));
		// and return a proper instance just for kicks
		return $this->retrieveDatabase($name);
		// TODO: catch exceptions?
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
		$result = $this->adapter->get($this->buildUri($name));
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
		$result = $this->adapter->delete($this->buildUri($name));
		return $result;
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
		return $this->get('_all_dbs')->toArray();
	}
	
	public function get($path)
	{
		$data = $this->adapter->get($this->baseUrl . $path);
		
		$retval = new \phpcouch\record\Record($this);
		$retval->fromArray($data);
		
		return $retval;
	}
}

?>