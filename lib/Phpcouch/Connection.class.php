<?php

class PhpcouchConnection
{
	protected $adapter = null;
	
	protected $database = null;
	
	protected $url = '';
	
	public function __construct(array $connectionInfo, PhpcouchAdapter $adapter = null)
	{
		if($adapter !== null) {
			$this->adapter = $adapter;
		} else {
			$this->adapter = new PhpcouchPhpAdapter();
		}
		
		$connectionInfo = array_merge(array(
			'scheme' => 'http',
			'host'   => 'localhost',
			'port'   => '8888'
		), $connectionInfo);
		
		if(!isset($connectionInfo['database'])) {
			throw new PhpcouchException('No database configured');
		}
		
		$this->setDatabase($connectionInfo['database']);
		
		$this->url = sprintf('%s://%s:%s/%s', $connectionInfo['scheme'], $connectionInfo['host'], $connectionInfo['port'], $this->getDatabase());
	}
	
	protected function buildUri(array $info = array())
	{
		return $this->url . (isset($info['id']) ? '/' . $info['id'] : '');
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
	}
	
	public function getAdapter()
	{
		return $this->adapter;
	}
	
	public function setAdapter(PhpcouchAdapter $adapter)
	{
		$this->adapter = $adapter;
	}
	
	public function getDatabase()
	{
		return $this->database;
	}
	
	public function setDatabase($database)
	{
		$this->database = $database;
	}
	
	public function create(PhpcouchDocument $document)
	{
		$values = $document->dehydrate();
		
		$this->sanitize($values);
		
		$values = json_encode($values);
		
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
			
			$result = json_decode($result);
			
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
		
		$result = json_decode($this->adapter->get($uri));
		
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
		
		$payload = json_encode($values);
		
		$uri = $this->buildUri(array('id' => $document->_id));
		
		$result = json_decode($this->adapter->put($uri, $payload));
		
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
		
		return json_decode($this->adapter->delete($uri));
	}
	
	public function newDocument()
	{
		return new PhpcouchDocument($this);
	}
}

?>