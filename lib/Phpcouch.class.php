<?php

/**
 * Main PHPCouch class.
 *
 * @author     David Zülke
 * @copyright  bitXtender GbR
 *
 * @since      0.1.0
 * @version    $Id$
 */
class Phpcouch
{
	const VERSION_NUMBER = '1.0.0';
	const VERSION_STATUS = 'dev';
	
	/**
	 * @var        array An array of class names and file paths for autoloading.
	 */
	protected static $autoloads = array();
	
	/**
	 * @var        array An array of registered connections.
	 */
	protected static $connections = array();
	
	/**
	 * @var        string The base filesystem path to the PHPCouch distribution.
	 */
	protected static $path = null;
	
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
			'PhpcouchIAdapter'              => 'Phpcouch/Adapter.interface.php',
			'PhpcouchCurlAdapter'           => 'Phpcouch/Adapter/Curl.class.php',
			'PhpcouchPeclhttpAdapter'       => 'Phpcouch/Adapter/Peclhttp.class.php',
			'PhpcouchZendhttpclientAdapter' => 'Phpcouch/Adapter/Zendhttpclient.class.php',
			'PhpcouchConfigurable'          => 'Phpcouch/Configurable.class.php',
			'PhpcouchConnection'            => 'Phpcouch/Connection.class.php',
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
		
		spl_autoload_register(array('PhpCouch', 'autoload'));
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
}

?>