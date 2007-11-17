<?php

class Phpcouch
{
	const VERSION_NUMBER = '1.0.0';
	const VERSION_STATUS = 'dev';
	
	protected static $autoloads = array();
	protected static $connections = array();
	protected static $path = null;
	protected static $options = array();
	protected static $readonlies = array();
	
	public static function autoload($className)
	{
		if(isset(self::$autoloads[$className])) {
			require(self::$path . '/' . self::$autoloads[$className]);
		}
	}
	
	public static function bootstrap()
	{
		self::$path = dirname(__FILE__);
		
		self::$autoloads = array(
			'PhpcouchAdapter'               => 'Phpcouch/Adapter.class.php',
			'PhpcouchCurlAdapter'           => 'Phpcouch/Adapter/Curl.class.php',
			'PhpcouchPeclhttpAdapter'       => 'Phpcouch/Adapter/Peclhttp.class.php',
			'PhpcouchZendhttpclientAdapter' => 'Phpcouch/Adapter/Zendhttpclient.class.php',
			'PhpcouchConnection'            => 'Phpcouch/Connection.class.php',
			'PhpcouchDatabase'              => 'Phpcouch/Database.class.php',
			'PhpcouchDocument'              => 'Phpcouch/Document.class.php',
			'PhpcouchException'             => 'Phpcouch/Exception.class.php',
			'PhpcouchAdapterException'      => 'Phpcouch/Exception/Adapter.class.php',
			'PhpcouchErrorException'        => 'Phpcouch/Exception/Error.class.php',
			'PhpcouchClientErrorException'  => 'Phpcouch/Exception/Error/Client.class.php',
			'PhpcouchServerErrorException'  => 'Phpcouch/Exception/Error/Server.class.php',
		);
		
		spl_autoload_register(array('PhpCouch', 'autoload'));
		
		self::$options = array(
		);
	}
	
	public static function getVersionInfo()
	{
		$retval = self::VERSION_NUMBER;
		
		if(self::VERSION_STATUS !== null) {
			$retval .= '-' . self::VERSION_STATUS;
		}
		
		return $retval;
	}
	
	public static function getVersionString()
	{
		return 'PHPCouch/' . self::getVersionInfo();
	}
	
	public static function registerConnection($name, PhpcouchConnection $connection)
	{
		self::$connections[$name] = $connection;
	}
	
	public static function unregisterConnection($name)
	{
		if(isset(self::$connections[$name])) {
			$retval = self::$connections[$name];
			unset(self::$connections[$name]);
			return $retval;
		}
	}
	
	public static function getConnection($name = 'default')
	{
		if(isset(self::$connections[$name])) {
			return self::$connections[$name];
		} else {
			throw new PhpcouchException(sprintf('Connection "%s" not configured.', $name));
		}
	}
	
	/**
	 * Get a configuration value.
	 *
	 * @param      string The name of the configuration directive.
	 *
	 * @return     mixed The value of the directive, or null if not set.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function getOption($name, $default = null)
	{
		if(isset(self::$options[$name])) {
			return self::$options[$name];
		} else {
			return $default;
		}
	}

	/**
	 * Check if a configuration directive has been set.
	 *
	 * @param      string The name of the configuration directive.
	 *
	 * @return     bool Whether the directive was set.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function hasOption($name)
	{
		return isset(self::$options[$name]);
	}

	/**
	 * Check if a configuration directive has been set as read-only.
	 *
	 * @param      string The name of the configuration directive.
	 *
	 * @return     bool Whether the directive is read-only.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function isOptionReadonly($name)
	{
		return isset(self::$readonlies[$name]);
	}

	/**
	 * Set a configuration value.
	 *
	 * @param      string The name of the configuration directive.
	 * @param      mixed  The configuration value.
	 * @param      bool   Whether or not an existing value should be overwritten.
	 * @param      bool   Whether or not this value should be read-only once set.
	 *
	 * @return     bool   Whether or not the configuration directive has been set.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function setOption($name, $value, $overwrite = true, $readonly = false)
	{
		$retval = false;
		if(($overwrite || !isset(self::$options[$name])) && !isset(self::$readonlies[$name])) {
			self::$options[$name] = $value;
			if($readonly) {
				self::$readonlies[$name] = $value;
			}
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
	 * @since      0.11.0
	 */
	public static function removeOption($name)
	{
		$retval = false;
		if(isset(self::$options[$name]) && !isset(self::$readonlies[$name])) {
			unset(self::$options[$name]);
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
	 * @since      0.11.0
	 */
	public static function setOptions($data)
	{
		self::$options = array_merge(array_merge(self::$options, $data), self::$readonlies);
	}

	/**
	 * Get all configuration directives and values.
	 *
	 * @return     array An associative array of configuration values.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function getOptions()
	{
		return self::$options;
	}

	/**
	 * Clear the configuration.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      0.11.0
	 */
	public static function clearOptions()
	{
		$restore = array_intersect_assoc(self::$readonlies, self::$options);
		self::$options = $restore;
	}
}

?>