<?php

namespace phpcouch;

interface RegistryInterface
{
	/**
	 * Get a configuration value.
	 *
	 * @param      string The name of the configuration directive.
	 * @param      string A default value for the configuration directive.
	 *
	 * @return     mixed The value of the directive, or null if not set.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getOption($name, $default = null);

	/**
	 * Check if a configuration directive has been set.
	 *
	 * @param      string The name of the configuration directive.
	 *
	 * @return     bool Whether the directive was set.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function hasOption($name);

	/**
	 * Set a configuration value.
	 *
	 * @param      string The name of the configuration directive.
	 * @param      mixed  The configuration value.
	 * @param      bool   Whether or not an existing value should be overwritten.
	 *
	 * @return     bool   Whether or not the configuration directive has been set.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setOption($name, $value, $overwrite = true);

	/**
	 * Remove a configuration value.
	 *
	 * @param      string The name of the configuration directive.
	 *
	 * @return     bool true, if removed successfuly, false otherwise.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function removeOption($name);

	/**
	 * Import a list of configuration directives.
	 *
	 * @param      string An array of configuration directives.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function setOptions($data);

	/**
	 * Get all configuration directives and values.
	 *
	 * @return     array An associative array of configuration values.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function getOptions();

	/**
	 * Clear the configuration.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public function clearOptions();
}

?>