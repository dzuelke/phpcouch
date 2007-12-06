<?php

class PhpcouchDocument extends PhpcouchMutableRecord implements PhpcouchIDocument
{
	const ATTACHMENTS_FIELD = '_attachments';
	const ID_FIELD = '_id';
	const REVISION_FIELD = '_rev';
	const REVISIONS_INFO_FIELD = '_revs_info';
	
	public function dehydrate()
	{
		$data = parent::dehydrate();
		
		foreach(array('_revs_info', '_revs') as $key) {
			// clean the flags that are returned for informational purposes
			if(array_key_exists($key, $data)) {
				unset($data[$key]);
			}
		}
		
		return $data;
	}
	
	public function hydrate($data)
	{
		parent::hydrate($data);
		
		$this->isNew = false;
		$this->isModified = false;
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
			// TODO: fetch revision info list
		}
	}
	
	public function save()
	{
		if($this->isNew()) {
			return $this->connection->createDocument($this);
		} elseif($this->isModified()) {
			return $this->connection->updateDocument($this);
		}
	}
}

?>