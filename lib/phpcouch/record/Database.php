<?php

namespace phpcouch\connection;

class Database extends ConnectionAbstract
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
	public function __construct(array $connectionInfo, \phpcouch\adapter\AdapterInterface $adapter = null)
	{
		parent::__construct($connectionInfo, $adapter);
		
		// got a database?
		if(!isset($connectionInfo['database'])) {
			// no :( bark!
			throw new \phpcouch\exception\Exception('No database set on connection');
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
	
	/**
	 * Get a list of all the documents in the database.
	 * 
	 * @param       PhpcouchIDocument The document to store.
	 *
	 * @return      stdClass list of all records
	 * 
	 * @throws      PhpcouchErrorException Yikes!
	 *
	 * @author      Simon Thulbourn
	 * @since       1.0.0
	 */
	public function listDocuments($allData = false)
	{
		$data = array();
		
		if ($allData) {
			$data = array('include_docs' => 'true');
		}
		
		try {
			$docs = $this->adapter->get($this->buildUri('_all_docs'));
			
			if($docs->total_rows == 0) {
				throw new \phpcouch\exception\error\Error('No documents founds');
			}
			
			if($allData) {
				foreach($docs->rows as &$row) {			
					$result = $this->adapter->get($this->buildUri($row->id));
					
					if(isset($result->_id)) {
						$document = $this->newDocument();
						$document->hydrate($result);
						$row = $document;
					} else {
						throw new \phpcouch\exception\error\Error('Something bad happened here, call the police');
					}
				}
			}
			
			return $docs;
		} catch (\phpcouch\exception\error\Error $e) {
			throw new \phpcouch\exception\error\Error($e->getMessage());
		}
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
	public function createDocument(phpcouch\Document $document)
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
				$document->hydrate(array(\phpcouch\Document::ID_FIELD => $result->id, \phpcouch\Document::REVISION_FIELD => $result->rev));
				return;
			} else {
				throw new PhpcouchSaveException();
				// TODO: add $result
			}
		} catch(\phpcouch\exception\error\Error $e) {
			throw new PhpcouchSaveException();
			// TODO: add $result
		}
	}
	
	/**
	 * Retrieve a document from the database.
	 *
	 * @param      string The ID of the document.
	 *
	 * @return     PhpcouchIDocument A document instance.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function retrieveDocument($id)
	{
		$uri = $this->buildUri($id);
		
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
	 *
	 * @return     string The attachment contents.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function retrieveAttachment($name, $id)
	{
		// TODO: this doesn't work atm
		if($id instanceof \phpcouch\DocumentInterface) {
			$id = $id->_id;
		}
		
		$uri = $this->buildUri($id, array('attachment' => $name));
		
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
	public function updateDocument(\phpcouch\DocumentInterface $document)
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
	 * @param      PhpcouchDocument The name of the document to delete.
	 *
	 * @return     PhpcouchIDocument The deletion stub document.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 * @since      1.0.0
	 */
	public function deleteDocument(\phpcouch\DocumentInterface $doc)
	{
		if($doc instanceof \phpcouch\DocumentInterface) {
			$headers = array('If-Match' => $doc->_rev);
			$id = $doc->_id;
		} else {
			throw new PhpcouchErrorException('Parameter supplied is not of type PhpcouchDocument');
		}
		
		$uri = $this->buildUri($id);
		return $this->adapter->delete($uri, $headers);
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
		return new \phpcouch\Document($this);
	}
}

?>