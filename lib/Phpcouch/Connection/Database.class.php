<?php

class PhpcouchDatabaseConnection extends PhpcouchConnection
{
	/**
	 * @var        string The name of the database to use with this connection by default.
	 */
	protected $database = '';
	
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
		parent::__construct($connectionInfo, $adapter);
		
		// got a database?
		if(!isset($connectionInfo['database'])) {
			// no :( bark!
			throw new PhpcouchException('No database set on connection');
		}
		// yes :) store the name...
		$this->database = $connectionInfo['database'];
		
		// ... and add it to the base URL
		$this->baseUrl .= $this->database . '/';
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
	
	public function listDocuments()
	{
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
	public function createDocument(PhpcouchDocument $document)
	{
		$values = $document->dehydrate();
		
		if(isset($values['_id'])) {
			// there is an id? nice, but we don't need it, the URL is enough
			unset($values['_id']);
		}
		
		try {
			if($document->_id) {
				// create a named document
				$uri = $this->buildUri($document->_id);
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
	public function retrieveDocument($id, $revision = null)
	{
		$uri = $this->buildUri($id, array('rev' => $revision, '_revs_info' => true));
		
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
		
		$uri = $this->buildUri($id, array('rev' => $revision, 'attachment' => $name));
		
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
	public function updateDocument(PhpcouchIDocument $document)
	{
		$values = $document->dehydrate();
		
		$uri = $this->buildUri($document->_id);
		
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
	public function deleteDocument($id)
	{
		if($id instanceof PhpcouchDocument) {
			$id = $id->_id;
		}
		
		$uri = $this->buildUri($id);
		
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