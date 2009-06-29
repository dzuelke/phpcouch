<?php

namespace phpcouch\record;

class ViewResult extends Record implements \IteratorAggregate
{
	protected $database;
	
	public function __construct(\phpcouch\record\Database $database = null)
	{
		parent::__construct($database->getConnection());
		
		$this->database = $database;
	}
	
	public function hydrate($data)
	{
		parent::hydrate($data);
		
		$newRows = array();
		// cannot iterate with &$row as it's a __get() function
		foreach($this->rows as $row) {
			$vrr = new ViewResultRow($this->database);
			$vrr->hydrate($row);
			$newRows[] = $vrr;
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