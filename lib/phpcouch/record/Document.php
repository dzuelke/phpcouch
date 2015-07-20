<?php

namespace phpcouch\record;

use phpcouch\UnexpectedValueException;

class Document extends MutableRecordAbstract implements DocumentInterface
{
	const ATTACHMENTS_FIELD = '_attachments';
	
	/**
	 * @var Database
	 */
	protected $database;
	
	/**
	 * @param Database $database
	 */
	public function __construct(Database $database = null)
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
	
	/**
	 * @return Database
	 */
	public function getDatabase()
	{
		return $this->database;
	}
	
	/**
	 * @return array
	 */
	public function getAttachments()
	{
		return $this->{self::ATTACHMENTS_FIELD};
	}
	
	/**
	 * @return int
	 */
	public function hasAttachments()
	{
		return count($this->{self::ATTACHMENTS_FIELD});
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAttachment($name)
	{
		return isset($this->{self::ATTACHMENTS_FIELD}->$name);
	}
	
	/**
	 * @param string $name
	 * @return mixed
	 * @throws \phpcouch\UnexpectedValueException
	 */
	public function retrieveAttachment($name)
	{
		if($this->hasAttachment($name)) {
			return $this->database->retrieveAttachment($name, $this);
		} else {
			throw new UnexpectedValueException(sprintf('Unknown attachment "%s".', $name));
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
