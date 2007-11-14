<?php

class PhpcouchConnection
{
	protected $adapter = null;
	
	public function __construct(PhpcouchAdapter $adapter)
	{
		$this->adapter = $adapter;
	}
	
	public function getDatabase()
	{
		return $this->adapter->getDatabase();
	}
	
	public function setDatabase($database)
	{
		return $this->adapter->setDatabase($database);
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
	
	public function create(PhpcouchDocument $document)
	{
		$values = $document->dehydrate();
		
		$this->sanitize($values);
		
		$options = array();
		// if(isset($values['_id'])) {
		// 	$options = array('id' => $values['_id']);
		// }
		
		$result = json_decode($this->adapter->post(json_encode($values), $options));
		
		if(isset($result->ok) && $result->ok === true) {
			$document->hydrate(array(PhpcouchDocument::ID_FIELD => $result->id, PhpcouchDocument::REVISION_FIELD => $result->rev));
		} else {
			// error
		}
	}
	
	public function retrieve($id, $revision = null)
	{
		$result = json_decode($this->adapter->get(array('id' => $id, 'revision' => $revision), array('_rev_info' => true)));
		
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
		
		return $this->adapter->get(array('id' => $id, 'revision' => $revision), array('attachment' => $name));
	}
	
	public function update(PhpcouchDocument $document)
	{
		$values = $document->dehydrate();
		
		$this->sanitize($values);
		
		$payload = json_encode($values);
		
		$result = json_decode($this->adapter->put($payload, array('id' => $document->_id)));
		
		if(isset($result->ok) && $result->ok === true) {
			$document->_rev = $result->_rev;
		} else {
			// error
		}
	}
	
	public function delete($id)
	{
		if($id instanceof PhpcouchDocument) {
			$id = $id->_id;
		}
		
		return json_decode($this->adapter->delete(array('id' => $id)));
	}
	
	public function newDocument()
	{
		return new PhpcouchDocument($this);
	}
}

?>