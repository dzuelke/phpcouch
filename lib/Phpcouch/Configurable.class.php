<?php

/**
 * A base class for extension that implements the PHPCouch Registry interface.
 *
 * @package    PHPCouch
 *
 * @author     David Zülke <dz@bitxtender.com>
 * @copyright  bitXtender GbR
 *
 * @since      1.0.0
 *
 * @version    $Id$
 */
abstract class PhpcouchConfigurable implements PhpcouchIRegistry
{
	/**
	 * @var        array An array of configuration options set on this object.
	 */
	protected $options = array();
	
	/**
	 * Get a configuration value.
	 *
	 * @param      string The name of the configuration directive.
	 *
	 * @return     mixed The value of the directive, or null if not set.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function getOption($name, $default = null)
	{
		if(isset($this->options[$name])) {
			return $this->options[$name];
		}
		return $default;
	}

	/**
	 * Check if a configuration directive has been set.
	 *
	 * @param      string The name of the configuration directive.
	 *
	 * @return     bool Whether the directive was set.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function hasOption($name)
	{
		return isset($this->options[$name]);
	}

	/**
	 * Set a configuration value.
	 *
	 * @param      string The name of the configuration directive.
	 * @param      mixed  The configuration value.
	 * @param      bool   Whether or not an existing value should be overwritten.
	 *
	 * @return     bool   Whether or not the configuration directive has been set.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function setOption($name, $value, $overwrite = true)
	{
		$retval = false;
		if(($overwrite || !isset($this->options[$name]))) {
			$this->options[$name] = $value;
			$retval = true;
		}
		return $retval;
	}

	/**
	 * Remove a configuration value.
	 *
	 * @param      string The name of the configuration directive.
	 *
	 * @return     bool true, if removed successfuly, false otherwise.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function removeOption($name)
	{
		$retval = false;
		if(isset($this->options[$name])) {
			unset($this->options[$name]);
			$retval = true;
		}
		return $retval;
	}

	/**
	 * Import a list of configuration directives.
	 *
	 * @param      string An array of configuration directives.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function setOptions($data)
	{
		$this->options = array_merge($this->options, $data);
	}

	/**
	 * Get all configuration directives and values.
	 *
	 * @return     array An associative array of configuration values.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Clear the configuration.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	public function clearOptions()
	{
		$this->options = array();
	}
}

?>