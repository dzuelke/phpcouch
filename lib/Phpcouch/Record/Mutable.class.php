<?php

abstract class PhpcouchMutableRecord extends PhpcouchRecord implements PhpcouchIMutableRecord
{
	/**
	 * @var        bool Flag indicating whether or not this record is new.
	 */
	protected $isNew = true;
	
	/**
	 * @var        bool Flag indicating whether or not this record is modified.
	 */
	protected $isModified = false;
	
	/**
	 * Indicates whether or not this record is new, i.e. never saved to the database before.
	 *
	 * @return     bool True, if this record is new.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function isNew()
	{
		return $this->isNew;
	}
	
	/**
	 * Indicates whether or not this record is modified, i.e. changed since the last save.
	 *
	 * @return     bool True, if this record is new.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function isModified()
	{
		return $this->isModified;
	}
	
	/**
	 * Set overload.
	 *
	 * @param      string Name of the virtual property to set.
	 * @param      mixed  The value to set.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function __set($name, $value)
	{
		if(!isset($this->data[$name]) || $this->data[$name] !== $value) {
			$this->isModified = true;
		}
		
		parent::__set($name, $value);
	}
	
	/**
	 * Unset overload.
	 *
	 * @param      string Name of the virtual property to unset.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function __unset($name)
	{
		if(array_key_exists($name, $this->data)) {
			$this->isModified = true;
		}
		
		parent::__unset($name);
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
		$data = $this->toArray();
		
		$remove = array();
		
		foreach($data as $key => $value) {
			if(strpos($key, '_') === 0 && $value === null) {
				// remember all internal CouchDB flags that do not have a value...
				$remove[] = $key;
			}
		}
		// and remove them
		foreach($remove as $key) {
			unset($data[$key]);
		}
		
		return $data;
	}
}

?>