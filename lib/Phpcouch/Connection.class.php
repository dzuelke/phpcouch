<?php

/**
 * The main connection class, representing a connection registered with PHPCouch.
 *
 * @package    PHPCouch
 *
 * @author     David Zülke <dz@bitxtender.com>
 * @copyright  bitXtender GbR
 *
 * @since      1.0.0
 *
 * @version    $Id$
 */
class PhpcouchConnection extends PhpcouchConfigurable
{
	/**
	 * @var        PhpcouchIAdapter An adapter to use with this connection.
	 */
	protected $adapter = null;
	
	/**
	 * @var        string The name of the database to use with this connection by default.
	 */
	protected $database = '';
	
	/**
	 * @var        string The base URL to the CouchDB server.
	 */
	protected $baseUrl = '';
	
	/**
	 * The connection constructor.
	 *
	 * @param      array            An array of connection information.
	 * @param      PhpcouchIAdapter The adapter to use with this connection, or null to use the default.
	 *
	 * @author     David Zülke
	 * @since      1.0.0
	 */
	public function __construct(array $connectionInfo, PhpcouchIAdapter $adapter = null)
	{
		if($adapter !== null) {
			$this->adapter = $adapter;
		} else {
			// no adapter given? let's create a default one.
			$this->adapter = new PhpcouchPhpAdapter();
		}
		
		// some default connection info for vanilla CouchDB setups
		$connectionInfo = array_merge(array(
			'scheme' => 'http',
			'host'   => 'localhost',
			'port'   => '8888',
		), $connectionInfo);
		
		// build the base URL from the connection info
		$this->baseUrl = sprintf('%s://%s:%s/', $connectionInfo['scheme'], $connectionInfo['host'], $connectionInfo['port']);
		
		// got a database?
		if(isset($connectionInfo['database'])) {
			// good, remember the name
			$this->setDatabase($connectionInfo['database']);
		}
	}
	
	/**
	 * Build a URI from the given information.
	 *
	 * @param      array An array of additional arguments to set in the URL.
	 *
	 * @return     string A generated URL.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	protected function buildUri(array $info = array())
	{
		// TODO: this sucks. we need support for query arguments. no no no, not gonna work :S
		if(isset($info['database'])) {
			$database = $info['database'];
		} elseif(!($database = $this->getDatabase())) {
			throw new PhpcouchException('No database set on connection');
		}
		return $this->baseUrl . $database . (isset($info['id']) ? '/' . $info['id'] : '');
	}
	
	/**
	 * Clean up the data before sending.
	 *
	 * @param      array The data array to clean up.
	 *
	 * @author     David Zülke
	 * @since      1.0.0
	 */
	protected function sanitize(array &$data)
	{
		$remove = array();
		
		foreach($data as $key => $value) {
			if(strpos($key, '_') === 0 && $value === null) {
				// remember all internal CouchDB flags that do not have as value...
				$remove[] = $key;
			}
		}
		// and remove them
		foreach($remove as $key) {
			unset($data[$key]);
		}
		
		foreach(array('_revs_info', '_revs') as $key) {
			// also, clean the flags that are returned for informational purposes
			if(array_key_exists($key, $data)) {
				unset($data[$key]);
			}
		}
	}
	
	/**
	 * Fetch the adapter used with this connection.
	 *
	 * @return     PhpcouchIAdapter The adapter instance.
	 *
	 * @author     David Zülke
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
	 * @author     David Zülke
	 * @since      1.0.0
	 */
	public function setAdapter(PhpcouchIAdapter $adapter)
	{
		$this->adapter = $adapter;
	}
	
	/**
	 * Get the name of the database to use with this connection.
	 *
	 * @return     string The database name.
	 *
	 * @author     David Zülke
	 * @since      1.0.0
	 */
	public function getDatabase()
	{
		return $this->database;
	}
	
	/**
	 * Set the name of the database to use with this connection.
	 *
	 * @param      string The database name.
	 *
	 * @author     David Zülke
	 * @since      1.0.0
	 */
	public function setDatabase($database)
	{
		// cast to string in case we're given a PhpcouchDatabase instance
		$this->database = (string)$database;
	}
	
	/**
	 * Create a new database on the server.
	 *
	 * @param      string The name of the database to create.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke
	 * @since      1.0.0
	 */
	public function createDatabase($name)
	{
		// result doesn't matter here
		$this->adapter->put($this->buildUri(array('database' => $name)));
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
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function retrieveDatabase($name)
	{
		// TODO: catch exceptions
		$result = $this->adapter->get($this->buildUri(array('database' => $name)));
		$database = new PhpcouchDatabase();
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
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function deleteDatabase($name)
	{
		// TODO: catch exceptions
		$result = $this->adapter->delete($this->buildUri(array('database' => $name)));
		return $result;
	}
	
	/**
	 * Create a new document on the server.
	 *
	 * @param      PhpcouchIDocument The document to store.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function create(PhpcouchDocument $document)
	{
		$values = $document->dehydrate();
		
		$this->sanitize($values);
		if(isset($values['_id'])) {
			// there is an id? nice, but we don't need it, the URL is enough
			unset($values['_id']);
		}
		
		try {
			if($document->_id) {
				// create a named document
				$uri = $this->buildUri(array('id' => $document->_id));
				$result = $this->adapter->put($uri, $values);
			} else {
				// let couchdb create an ID
				$uri = $this->buildUri();
				$result = $this->adapter->post($uri, $values);
			}
			
			if(isset($result->ok) && $result->ok === true) {
				// all cool.
				$document->hydrate(array(PhpcouchDocument::ID_FIELD => $result->id, PhpcouchDocument::REVISION_FIELD => $result->rev));
				return;
			} else {
				throw new PhpcouchSaveException();
				// TODO: add $result
			}
		} catch(PhpcouchErrorException $e) {
			throw new PhpcouchSaveException();
			// TODO: add $result
		}
	}
	
	/**
	 * Retrieve a document from the database.
	 *
	 * @param      string The ID of the document.
	 * @param      string The revision to fetch (default is latest).
	 *
	 * @return     PhpcouchIDocument A document instance.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function retrieve($id, $revision = null)
	{
		$uri = $this->buildUri(array('id' => $id), array('rev' => $revision, '_revs_info' => true));
		
		// TODO: grab and wrap exceptions
		$result = $this->adapter->get($uri);
		
		if(isset($result->_id)) {
			$document = $this->newDocument();
			$document->hydrate($result);
			return $document;
		} else {
			// error
		}
	}
	
	/**
	 * Retrieve an attachment of a document.
	 *
	 * @param      string The name of the attachment.
	 * @param      string The document ID.
	 * @param      string The document revision (default is latest).
	 *
	 * @return     string The attachment contents.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function retrieveAttachment($name, $id, $revision = null)
	{
		// TODO: this doesn't work atm
		if($id instanceof PhpcouchDocument) {
			$id = $id->_id;
			if($revision !== null) {
				$revision = $id->_rev;
			}
		}
		
		$uri = $this->buildUri(array('id' => $id), array('rev' => $revision, 'attachment' => $name));
		
		return $this->adapter->get($uri);
	}
	
	/**
	 * Save a modified document to the database.
	 *
	 * @param      PhpcouchIDocument The document to save.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function update(PhpcouchIDocument $document)
	{
		$values = $document->dehydrate();
		
		$this->sanitize($values);
		
		$uri = $this->buildUri(array('id' => $document->_id));
		
		$result = $this->adapter->put($uri, $values);
		
		if(isset($result->ok) && $result->ok === true) {
			$document->_rev = $result->rev;
		} else {
			// error
		}
	}
	
	/**
	 * Delete a document.
	 *
	 * @param      string The name of the document to delete.
	 *
	 * @return     PhpcouchIDocument The deletion stub document.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function delete($id)
	{
		if($id instanceof PhpcouchDocument) {
			$id = $id->_id;
		}
		
		$uri = $this->buildUri(array('id' => $id));
		
		return $this->adapter->delete($uri);
	}
	
	/**
	 * Make a new document instance with this connection set on it.
	 *
	 * @return     PhpcouchIDocument An empty document.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function newDocument()
	{
		return new PhpcouchDocument($this);
	}
}

?>