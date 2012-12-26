<?php

namespace phpcouch\record;

interface RecordInterface
{
	/**
	 * Get overload.
	 *
	 * @param      string Name of the virtual property to fetch.
	 *
	 * @return     mixed The property value.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __get($name);
	
	/**
	 * Isset overload.
	 *
	 * @param      string Name of the virtual property to check for existance.
	 *
	 * @return     bool Whether or not a property of that name exists.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __isset($name);
	
	/**
	 * Set overload.
	 *
	 * @param      string Name of the virtual property to set.
	 * @param      mixed  The value to set.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __set($name, $value);
	
	/**
	 * Unset overload.
	 *
	 * @param      string Name of the virtual property to unset.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __unset($name);
	
	/**
	 * Import data into this record.
	 *
	 * @param      mixed The data to load (array, PhpcouchIRecord or object)
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function fromArray($data);
	
	/**
	 * Export data from this record.
	 *
	 * @return     array An array representation of this record's data.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function toArray();
	
	/**
	 * Load data into this record.
	 * This will clear all information before importing the data.
	 *
	 * @param      array The data to load.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function hydrate($data);
	
	/**
	 * Retrieve the connection associated with this record.
	 *
	 * @return     \phpcouch\connection\Connection The connection used by this record.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getConnection();
	
	/**
	 * Set the connection to be used with this record.
	 *
	 * @param      \phpcouch\connection\Connection The connection to associate.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setConnection(\phpcouch\connection\Connection $connection = null);
}

?>