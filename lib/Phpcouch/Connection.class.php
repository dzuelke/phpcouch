<?php

class PhpcouchConnection
{
	protected $adapter = null;
	
	protected $database = '';
	
	protected $baseUrl = '';
	
	public function __construct(array $connectionInfo, PhpcouchIAdapter $adapter = null)
	{
		if($adapter !== null) {
			$this->adapter = $adapter;
		} else {
			$this->adapter = new PhpcouchPhpAdapter();
		}
		
		$connectionInfo = array_merge(array(
			'scheme' => 'http',
			'host'   => 'localhost',
			'port'   => '8888',
		), $connectionInfo);
		
		$this->baseUrl = sprintf('%s://%s:%s/', $connectionInfo['scheme'], $connectionInfo['host'], $connectionInfo['port']);
		
		if(isset($connectionInfo['database'])) {
			$this->setDatabase($connectionInfo['database']);
		}
	}
	
	protected function buildUri(array $info = array())
	{
		if(isset($info['database'])) {
			$database = $info['database'];
		} elseif(!($database = $this->getDatabase())) {
			throw new PhpcouchException('No database set on connection');
		}
		return $this->baseUrl . $database . (isset($info['id']) ? '/' . $info['id'] : '');
	}
	
	protected function sanitize(array &$data)
	{
		$remove = array();
		
		foreach($data as $key => $value) {
			if(strpos($key, '_') === 0 && $value === null) {
				$remove[] = $key;
			}
		}
		
		foreach($remove as $key) {
			unset($data[$key]);
		}
		
		foreach(array('_revs_info', '_revs') as $key) {
			if(array_key_exists($key, $data)) {
				unset($data[$key]);
			}
		}
	}
	
	public function getAdapter()
	{
		return $this->adapter;
	}
	
	public function setAdapter(PhpcouchIAdapter $adapter)
	{
		$this->adapter = $adapter;
	}
	
	public function getDatabase()
	{
		return $this->database;
	}
	
	public function setDatabase($database)
	{
		$this->database = (string)$database;
	}
	
	public function createDatabase($name)
	{
		// result doesn't matter here
		$this->adapter->put($this->buildUri(array('database' => $name)));
		return $this->retrieveDatabase($name);
		// TODO: catch exceptions?
	}
	
	public function retrieveDatabase($name)
	{
		$result = $this->adapter->get($this->buildUri(array('database' => $name)));
		$database = new PhpcouchDatabase();
		$database->hydrate($result);
		return $database;
	}
	
	public function deleteDatabase($name)
	{
		$result = $this->adapter->delete($this->buildUri(array('database' => $name)));
		return $result;
	}
	
	public function create(PhpcouchDocument $document)
	{
		$values = $document->dehydrate();
		
		$this->sanitize($values);
		if(isset($values['_id'])) {
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
				$document->hydrate(array(PhpcouchDocument::ID_FIELD => $result->id, PhpcouchDocument::REVISION_FIELD => $result->rev));
				return;
			} else {
				throw new PhpcouchSaveException();
				// TODO: add $result
			}
		} catch(PhpcouchServerException $e) {
			$result = json_decode($e->getServerResponse());
			throw new PhpcouchSaveException();
			// TODO: add $result
		}
	}
	
	public function retrieve($id, $revision = null)
	{
		$uri = $this->buildUri(array('id' => $id), array('rev' => $revision, '_revs_info' => true));
		
		$result = $this->adapter->get($uri);
		
		if(isset($result->_id)) {
			$document = $this->newDocument();
			$document->hydrate($result);
			return $document;
		} else {
			// error
		}
	}
	
	public function retrieveAttachment($name, $id, $revision = null)
	{
		if($id instanceof PhpcouchDocument) {
			$id = $id->_id;
			if($revision !== null) {
				$revision = $id->_rev;
			}
		}
		
		$uri = $this->buildUri(array('id' => $id), array('rev' => $revision, 'attachment' => $name));
		
		return $this->adapter->get($uri);
	}
	
	public function update(PhpcouchDocument $document)
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
	
	public function delete($id)
	{
		if($id instanceof PhpcouchDocument) {
			$id = $id->_id;
		}
		
		$uri = $this->buildUri(array('id' => $id));
		
		return $this->adapter->delete($uri);
	}
	
	public function newDocument()
	{
		return new PhpcouchDocument($this);
	}
}

?>