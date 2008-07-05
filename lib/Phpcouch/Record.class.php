<?php

abstract class PhpcouchRecord implements PhpcouchIRecord
{
	/**
	 * @var        PhpcouchConnection The connection associated with this record.
	 */
	protected $connection = null;
	
	/**
	 * @var        array The data array for magic methods.
	 */
	protected $data = array();
	
	/**
	 * Record constructor.
	 *
	 * @param      PhpcouchConnection An optional connection to associate with this record.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __construct(PhpcouchConnection $connection = null)
	{
		$this->setConnection($connection);
	}
	
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
	public function __get($name)
	{
		if(isset($this->data[$name])) {
			return $this->data[$name];
		}
	}
	
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
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}
	
	/**
	 * Set overload.
	 *
	 * @param      string Name of the virtual property to set.
	 * @param      mixed  The value to set.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}
	
	/**
	 * Unset overload.
	 *
	 * @param      string Name of the virtual property to unset.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __unset($name)
	{
		if(array_key_exists($name, $this->data)) {
			unset($this->data[$name]);
		}
	}
	
	/**
	 * Load data into this record.
	 * This will clear all information before importing the data.
	 *
	 * @param      array The data to load.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function hydrate($data)
	{
		$this->data = array();
		
		$this->fromArray($data);
	}
	
	/**
	 * Import data into this record.
	 *
	 * @param      mixed The data to load (array, PhpcouchIRecord or object)
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function fromArray($data)
	{
		if($data instanceof PhpcouchIRecord) {
			$data = $data->toArray();
		} elseif(is_object($data)) {
			$data = get_object_vars($data);
		}
		
		foreach($data as $key => $value) {
			if(strpos($key, '_') === 0) {
				$this->{$key} = $value;
			} else {
				$this->__set($key, $value);
			}
		}
	}
	
	/**
	 * Export data from this record.
	 *
	 * @return     array An array representation of this record's data.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function toArray()
	{
		$retval = array();
		
		foreach($this->data as $key => $value) {
			$retval[$key] = $this->__get($key);
		}
		
		return $retval;
	}
	
	/**
	 * Retrieve the connection associated with this record.
	 *
	 * @return     PhpcouchConnection The connection used by this record.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getConnection()
	{
		return $this->connection;
	}
	
	/**
	 * Set the connection to be used with this record.
	 *
	 * @param      PhpcouchConnection The connection to associate.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setConnection(PhpcouchConnection $connection = null)
	{
		if($connection === null) {
			$connection = Phpcouch::getConnection();
		}
		
		$this->connection = $connection;
	}
}

?>