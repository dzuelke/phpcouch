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
	 * @param      array            An array of connection information.
	 * @param      PhpcouchIAdapter The adapter to use with this connection, or null to use the default.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __construct(array $connectionInfo, \phpcouch\adapter\AdapterInterface $adapter = null)
	{
		if($adapter !== null) {
			$this->adapter = $adapter;
		} else {
			// no adapter given? let's create a default one.
			$this->adapter = new \phpcouch\adapter\Php();
		}
		
		// some default connection info for vanilla CouchDB setups
		$connectionInfo = array_merge(array(
			'scheme' => 'http',
			'host'   => 'localhost',
			'port'   => self::COUCHDB_DEFAULT_PORT,
		), $connectionInfo);
		
		// build the base URL from the connection info
		$this->baseUrl = sprintf('%s://%s:%s/', $connectionInfo['scheme'], $connectionInfo['host'], $connectionInfo['port']);
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
	protected function buildUri($id = null, array $arguments = array())
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
	 * Set an adapter to use with this connection.
	 *
	 * @param      PhpcouchIAdapter The adapter instance to use.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setAdapter(\phpcouch\adapter\AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
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
		$database = new phpcouch\Database($this);
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
		// TODO: catch exceptions?
		return $this->adapter->get($this->buildUri('_all_dbs'));
	}
}

?>