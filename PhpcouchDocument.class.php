<?php

class PhpcouchDocument
{
	const ATTACHMENTS_FIELD = '_attachments';
	const ID_FIELD = '_id';
	const REVISION_FIELD = '_rev';
	const REVISIONS_INFO_FIELD = '_revs_info';
	
	protected $connection = null;
	protected $data = array();
	protected $isNew = true;
	protected $isModified = false;
	
	public $_attachments = null;
	public $_id = null;
	public $_rev = null;
	public $_revs_info = null;
	
	public function __construct(PhpcouchConnection $connection = null)
	{
		if($connection === null) {
			$connection = Phpcouch::getConnection();
		}
		$this->connection = $connection;
		
		// some basic init
		// $this->__set(self::ID_FIELD, null);
		// $this->__set(self::REVISION_FIELD, null);
		// $this->__set(self::ATTACHMENTS_FIELD, null);
		// $this->__set(self::REVISIONS_INFO_FIELD, null);
	}
	
	public function __get($name)
	{
		if(isset($this->data[$name])) {
			return $this->data[$name];
		}
	}
	
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}
	
	public function __set($name, $value)
	{
		if(!isset($this->data[$name]) || $this->data[$name] !== $value) {
			$this->isModified = true;
		}
		$this->data[$name] = $value;
	}
	
	public function __unset($name)
	{
		if(array_key_exists($this->data[$name])) {
			unset($this->data[$name]);
		}
	}
	
	public function hydrate($data)
	{
		if(is_object($data)) {
			$data = get_object_vars($data);
		}
		
		$this->fromArray($data);
		
		$this->isNew = false;
		$this->isSaved = true;
	}
	
	public function dehydrate()
	{
		return $this->toArray();
	}
	
	public function fromArray(array $data)
	{
		foreach($data as $key => $value) {
			if(strpos($key, '_') === 0) {
				$this->{$key} = $value;
			} else {
				$this->__set($key, $value);
			}
		}
	}
	
	public function toArray()
	{
		$retval = array();
		
		foreach($this->data as $key => $value) {
			$retval[$key] = $this->__get($key);
		}
		
		foreach(get_object_vars($this) as $key => $value) {
			if(strpos($key, '_') === 0) {
				$retval[$key] = $value;
			}
		}
		
		return $retval;
	}
	
	public function isNew()
	{
		return $this->isNew;
	}
	
	public function isSaved()
	{
		return $this->isSaved;
	}
	
	public function getAttachments()
	{
		return $this->{self::ATTACHMENTS_FIELD};
	}
	
	public function hasAttachments()
	{
		return count($this->{self::ATTACHMENTS_FIELD});
	}
	
	public function retrieveAttachment($name)
	{
		if(isset($this->{self::ATTACHMENTS_FIELD}[$name])) {
			return $this->connection->retrieveAttachment($this, $name);
		} else {
			throw new PhpcouchException(sprintf('Unknown attachment "%s".', $name));
		}
	}
	
	public function retrieveRevision($revision)
	{
		$this->connection->retrieve($this, $revision);
	}
	
	public function retrieveRevisionInfoList()
	{
		if(isset($this->{self::REVISION_INFO_FIELD})) {
			return $this->{self::REVISION_INFO_FIELD};
		} else {
			// TODO: grab revision info list
		}
	}
	
	public function save()
	{
		if($this->isNew()) {
			return $this->connection->create($this);
		} else {
			return $this->connection->update($this);
		}
	}
}

?>