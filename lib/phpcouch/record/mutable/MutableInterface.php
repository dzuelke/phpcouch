<?php

interface PhpcouchIMutableRecord extends PhpcouchIRecord
{
	/**
	 * Indicates whether or not this record is new, i.e. never saved to the database before.
	 *
	 * @return     bool True, if this record is new.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function isNew();
	
	/**
	 * Indicates whether or not this record is modified, i.e. changed since the last save.
	 *
	 * @return     bool True, if this record is new.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function isModified();
	
	/**
	 * Dehydrate record data into a saveable array.
	 *
	 * @return     array The cleaned data array.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function dehydrate();
	
	/**
	 * Save this record on the server.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function save();
}

?>