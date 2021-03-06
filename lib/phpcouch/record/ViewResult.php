<?php

namespace phpcouch\record;

class ViewResult extends Record implements ViewResultInterface, \IteratorAggregate
{
	const DEFAULT_VIEW_RESULT_ROW_CLASS = 'phpcouch\record\ViewResultRow';
	
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
	 * @return Database
	 */
	public function getDatabase()
	{
		return $this->database;
	}
	
	public function hydrate($data)
	{
		parent::hydrate($data);
		
		$newRows = array();
		$cls = static::DEFAULT_VIEW_RESULT_ROW_CLASS;
		
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