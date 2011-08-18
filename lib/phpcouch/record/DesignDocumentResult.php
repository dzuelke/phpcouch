<?php

namespace phpcouch\record;

class DesignDocumentResult extends Record implements DesignDocumentResultInterface, \IteratorAggregate
{
	const DEFAULT_DESIGNDOCUMENT_RESULT_ROW_CLASS = 'phpcouch\record\DesignDocumentResultRow';
	
	protected $database;
	
	public function __construct(Database $database = null)
	{
		parent::__construct($database->getConnection());
		
		$this->database = $database;
	}
	
	public function getDatabase()
	{
		return $this->database;
	}
	
	public function hydrate($data)
	{
		parent::hydrate($data);
		
		$newRows = array();
		$cls = static::DEFAULT_DESIGNDOCUMENT_RESULT_ROW_CLASS;
		
		if (isset($this->rows)) {
			// cannot iterate with &$row as it's a __get() function
			foreach($this->rows as $row) {
				$vrr = new $cls($this);
				$vrr->hydrate($row);
				$newRows[] = $vrr;
			}
		}
		$this->rows = $newRows;
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->getRows());
	}
	
	public function getOffset()
	{
		return $this->offset;
	}
	
	public function getTotalRows()
	{
		return $this->total_rows;
	}
	
	public function getRows()
	{
		return $this->rows;
	}
}

?>