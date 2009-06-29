<?php

namespace phpcouch;

const VERSION_NUMBER = '1.0.0';
const VERSION_STATUS = 'dev';

/**
 * Main PHPCouch class.
 *
 * @package    PHPCouch
 *
 * @author     David Zülke <david.zuelke@bitextender.com>
 * @copyright  Bitextender GmbH
 *
 * @since      1.0.0
 *
 * @version    $Id$
 */
class Phpcouch
{
	/**
	 * @var        array An array of registered connections.
	 */
	protected static $connections = array();
	
	/**
	 * @var        string The base filesystem path to the PHPCouch distribution.
	 */
	protected static $path = null;
	
	/**
	 * @var        string The name of the default connection.
	 */
	protected static $defaultConnection = null;
	
	/**
	 * PHPCouch autoloader.
	 *
	 * @param      string The name of the class to autoload.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public static function autoload($className)
	{
		if(strpos($className, 'phpcouch\\') === 0) {
			$path = self::$path . '/' . str_replace(array('\\', '_'), '/', substr($className, 9)) . '.php';
			if(file_exists($path)) {
				require($path);
			}
		}
	}
	
	/**
	 * Main PHPCouch initialization method.
	 *
	 * This sets up the base path and registers the autoloader.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public static function bootstrap()
	{
		// grab the base path where we are located
		self::$path = dirname(__FILE__);
		
		// and register our autoloader
		spl_autoload_register(array(__CLASS__, 'autoload'));
	}
	
	/**
	 * Version information method.
	 *
	 * Returns version number along with the version status, if applicable.
	 *
	 * @return     string A version number, including status if applicable, e.g. "1.2.0-RC2".
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public static function getVersionInfo()
	{
		$retval = VERSION_NUMBER;
		
		// only append a status (like "RC3") if it is set
		if(VERSION_STATUS !== null) {
			$retval .= '-' . VERSION_STATUS;
		}
		
		return $retval;
	}
	
	/**
	 * Full version information string method.
	 *
	 * Returns the product name and the version number along with the version status, if applicable.
	 *
	 * @return     string A full version string, example: "PHPCouch/1.0.0".
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public static function getVersionString()
	{
		// a slash is common, e.g. Apache/2.2.23 or PHP/5.2.4, so we do that too
		return 'PHPCouch/' . self::getVersionInfo();
	}
	
	/**
	 * Register a connection.
	 *
	 * @param      string                         The name of the connection.
	 * @param      phpcouch\connection\Connection A connection instance.
	 * @param      bool                           Whether or not to make this connection the default one.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public static function registerConnection($name, connection\Connection $connection, $default = true)
	{
		self::$connections[$name] = $connection;
		
		if($default) {
			self::$defaultConnection = $name;
		}
	}
	
	/**
	 * Unregister a previously registered connection.
	 *
	 * @param      string The name of the connection to remove.
	 *
	 * @return     phpcouch\connection\Connection The connection instance that was removed from the pool, or null if no connection of that name was registered.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public static function unregisterConnection($name)
	{
		if(isset(self::$connections[$name])) {
			// remember the value we are about to remove...
			$retval = self::$connections[$name];
			unset(self::$connections[$name]);
			if($name == self::$defaultConnection) {
				// clear the default connection
				self::$defaultConnection = null;
			}
			// ...and return it
			return $retval;
		}
	}
	
	/**
	 * Retrieve a registered connection instance.
	 *
	 * @param      string The name of the connection, or null (default) if the default connection should be returned.
	 *
	 * @return     phpcouch\connection\Connection A connection instance, if found.
	 *
	 * @throws     PhpcouchException If no connection of this name was configured.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public static function getConnection($name = null)
	{
		if($name === null) {
			$name = self::$defaultConnection;
		}
		
		if($name !== null && isset(self::$connections[$name])) {
			return self::$connections[$name];
		} elseif($name === null) {
			throw new Exception(sprintf('No default connection defined.'));
		} else {
			throw new Exception(sprintf('Connection "%s" not configured.', $name));
		}
	}
	
	public static function clearConnections()
	{
		self::$connections = array();
		
		self::$defaultConnection = null;
	}
}

?>
