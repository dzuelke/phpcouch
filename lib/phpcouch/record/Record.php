<?php

namespace phpcouch\record;

use phpcouch\http\HttpResponse;

class Record implements RecordInterface, \ArrayAccess
{
	/**
	 * @var        \phpcouch\connection\Connection The connection associated with this record.
	 */
	protected $connection = null;
	
	/**
	 * @var        array The data array for magic methods.
	 */
	protected $data = array();
	
	/**
	 * Record constructor.
	 *
	 * @param      \phpcouch\connection\Connection An optional connection to associate with this record.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function __construct(\phpcouch\connection\Connection $connection = null)
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
	 * @param      string Name of the virtual property to check for existence.
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
	
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}
	
	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}
	
	public function offsetSet($offset, $value)
	{
		return $this->__set($offset, $value);
	}
	
	public function offsetUnset($offset)
	{
		return $this->__unset($offset);
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
		if($data instanceof RecordInterface) {
			$data = $data->toArray();
		} elseif($data instanceof HttpResponse && $data->getContentType() == 'application/json') {
			$data = json_decode($data->getContent(), $this->connection->getOption('use_arrays', false));
		} elseif(is_object($data)) {
			$data = get_object_vars($data);
		}
		
		foreach($data as $key => $value) {
			$this->__set($key, $value);
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
		if($this->connection && $this->connection->getOption('use_arrays', false)) {
			return $this->data;
		} else {
			$retval = array();
			foreach($this->data as $key => $value) {
				$val = $this->__get($key);
				$val = $this->objectToArray($val);
				$retval[$key] = $val;
			}
		}
		return $retval;
	}
	
	/**
	 * Converts object to an array
	 *
	 * @param      mixed object to be converted
	 * 
	 * @return     mixed
	 * 
	 * @author     Niklas Närhinen <niklas@narhinen.net>
	 * @since      1.0.0
	 */
	protected function objectToArray($obj)
	{
		if(is_object($obj)) {
			$obj = get_object_vars($obj);
		} 
		
		if(is_array($obj)) {
			return array_map(array($this, 'objectToArray'), $obj);
		} else {
			return $obj;
		}
	}
	
	/**
	 * Retrieve the connection associated with this record.
	 *
	 * @return     \phpcouch\connection\Connection The connection used by this record.
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
	 * @param      \phpcouch\connection\Connection The connection to associate.
	 *
	 * @throws     ?
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setConnection(\phpcouch\connection\Connection $connection = null)
	{
		if($connection === null) {
			$connection = \phpcouch\Phpcouch::getConnection();
		}
		
		$this->connection = $connection;
	}
}

?>