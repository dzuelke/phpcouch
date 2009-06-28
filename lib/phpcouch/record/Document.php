<?php

namespace phpcouch\record;

use phpcouch\exception;

class Document extends MutableRecordAbstract implements DocumentInterface
{
	const ATTACHMENTS_FIELD = '_attachments';
	
	protected $database;
	
	public function __construct(\phpcouch\record\Database $database = null)
	{
		parent::__construct($database->getConnection());
		
		$this->database = $database;
	}
	
	/**
	 * Load data into this record.
	 * This will clear all information before importing the data and set new and modified flags to false.
	 *
	 * @param      array The data to load.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
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
			throw new Exception(sprintf('Unknown attachment "%s".', $name));
		}
	}
	
	/**
	 * Save this document in the database.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function save()
	{
		if($this->isNew()) {
			return $this->database->createDocument($this);
		} elseif($this->isModified()) {
			return $this->database->updateDocument($this);
		}
	}
}

?>