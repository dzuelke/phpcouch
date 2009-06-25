<?php

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
	const VERSION_NUMBER = '1.0.0';
	const VERSION_STATUS = 'dev';
	
	/**
	 * @var        array An array of class names and file paths for autoloading.
	 */
	protected static $autoloads = array(
		'PhpcouchIAdapter'              => 'Phpcouch/Adapter.interface.php',
		'PhpcouchCurlAdapter'           => 'Phpcouch/Adapter/Curl.class.php',
		'PhpcouchPeclhttpAdapter'       => 'Phpcouch/Adapter/Peclhttp.class.php',
		'PhpcouchPhpAdapter'            => 'Phpcouch/Adapter/Php.class.php',
		'PhpcouchZendhttpclientAdapter' => 'Phpcouch/Adapter/Zendhttpclient.class.php',
		'PhpcouchConfigurable'          => 'Phpcouch/Configurable.class.php',
		'PhpcouchConnection'            => 'Phpcouch/Connection.class.php',
		'PhpcouchDatabaseConnection'    => 'Phpcouch/Connection/Database.class.php',
		'PhpcouchServerConnection'      => 'Phpcouch/Connection/Server.class.php',
		'PhpcouchDatabase'              => 'Phpcouch/Database.class.php',
		'PhpcouchIDocument'             => 'Phpcouch/Document.interface.php',
		'PhpcouchDocument'              => 'Phpcouch/Document.class.php',
		'PhpcouchException'             => 'Phpcouch/Exception.class.php',
		'PhpcouchAdapterException'      => 'Phpcouch/Exception/Adapter.class.php',
		'PhpcouchErrorException'        => 'Phpcouch/Exception/Error.class.php',
		'PhpcouchClientErrorException'  => 'Phpcouch/Exception/Error/Client.class.php',
		'PhpcouchServerErrorException'  => 'Phpcouch/Exception/Error/Server.class.php',
		'PhpcouchIRecord'               => 'Phpcouch/Record.interface.php',
		'PhpcouchIMutableRecord'        => 'Phpcouch/Record/Mutable.interface.php',
		'PhpcouchRecord'                => 'Phpcouch/Record.class.php',
		'PhpcouchMutableRecord'         => 'Phpcouch/Record/Mutable.class.php',
		'PhpcouchIRegistry'             => 'Phpcouch/Registry.interface.php',
	);
	
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
		if(isset(self::$autoloads[$className])) {
			require(self::$path . '/' . self::$autoloads[$className]);
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
		spl_autoload_register(array('PhpCouch', 'autoload'));
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
		$retval = self::VERSION_NUMBER;
		
		// only append a status (like "RC3") if it is set
		if(self::VERSION_STATUS !== null) {
			$retval .= '-' . self::VERSION_STATUS;
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
	 * @param      string             The name of the connection.
	 * @param      PhpcouchConnection A connection instance.
	 * @param      bool               Whether or not to make this connection the default one.
	 *
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 * @since      1.0.0
	 */
	public static function registerConnection($name, PhpcouchConnection $connection, $default = true)
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
	 * @return     PhpcouchConnection The connection instance that was removed from the pool, or null if no connection of that name was registered.
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
	 * @return     PhpcouchConnection A connection instance, if found.
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
			throw new PhpcouchException(sprintf('No default connection defined.'));
		} else {
			throw new PhpcouchException(sprintf('Connection "%s" not configured.', $name));
		}
	}
	
	public static function clearConnections()
	{
		self::$connections = array();
		
		self::$defaultConnection = null;
	}
}

?>