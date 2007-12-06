<?php

class PhpcouchDocument extends PhpcouchMutableRecord implements PhpcouchIDocument
{
	const ATTACHMENTS_FIELD = '_attachments';
	
	/**
	 * Dehydrate record data into a saveable array.
	 *
	 * @return     array The cleaned data array.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
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
	
	/**
	 * Load data into this record.
	 * This will clear all information before importing the data and set new and modified flags to false.
	 *
	 * @param      array The data to load.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
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
	
	public function retrieveRevisionInfoList()
	{
		if(isset($this->{self::REVISION_INFO_FIELD})) {
			return $this->{self::REVISION_INFO_FIELD};
		} else {
			// TODO: fetch revision info list
		}
	}
	
	/**
	 * Save this document in the database.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
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