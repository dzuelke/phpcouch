<?php

class PhpcouchDocument extends PhpcouchMutableRecord implements PhpcouchIDocument
{
	const ATTACHMENTS_FIELD = '_attachments';
	const ID_FIELD = '_id';
	const REVISION_FIELD = '_rev';
	const REVISIONS_INFO_FIELD = '_revs_info';
	
	public $_attachments = null;
	public $_id = null;
	public $_rev = null;
	public $_revs_info = null;
	
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
			// TODO: grab revision info list
		}
	}
	
	public function save()
	{
		if($this->isNew()) {
			return $this->connection->create($this);
		} elseif($this->isModified()) {
			return $this->connection->update($this);
		}
	}
}

?>