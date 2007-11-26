<?php

/**
 * The main connection class, representing a connection registered with PHPCouch.
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
abstract class PhpcouchConnection extends PhpcouchConfigurable
{
	/**
	 * @var        PhpcouchIAdapter An adapter to use with this connection.
	 */
	protected $adapter = null;
	
	/**
	 * @var        string The base URL for this connection.
	 */
	protected $baseUrl = '';
	
	/**
	 * The connection constructor.
	 *
	 * @param      array            An array of connection information.
	 * @param      PhpcouchIAdapter The adapter to use with this connection, or null to use the default.
	 *
	 * @author     David Zülke
	 * @since      1.0.0
	 */
	public function __construct(array $connectionInfo, PhpcouchIAdapter $adapter = null)
	{
		if($adapter !== null) {
			$this->adapter = $adapter;
		} else {
			// no adapter given? let's create a default one.
			$this->adapter = new PhpcouchPhpAdapter();
		}
		
		// some default connection info for vanilla CouchDB setups
		$connectionInfo = array_merge(array(
			'scheme' => 'http',
			'host'   => 'localhost',
			'port'   => '8888',
		), $connectionInfo);
		
		// build the base URL from the connection info
		$this->baseUrl = sprintf('%s://%s:%s/', $connectionInfo['scheme'], $connectionInfo['host'], $connectionInfo['port']);
	}
	
	/**
	 * Build a URI from the given information.
	 *
	 * @param      string The ID of the entity to fetch.
	 * @param      array  An array of additional arguments to set in the URL.
	 *
	 * @return     string A generated URL.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @since      1.0.0
	 */
	protected function buildUri($id = null, array $arguments = array())
	{
		return sprintf('%s%s?%s',
			$this->baseUrl,
			$id,
			http_build_query($arguments)
		);
	}
	
	/**
	 * Clean up the data before sending.
	 *
	 * @param      array The data array to clean up.
	 *
	 * @author     David Zülke
	 * @since      1.0.0
	 */
	protected function sanitize(array &$data)
	{
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
	}
	
	/**
	 * Fetch the adapter used with this connection.
	 *
	 * @return     PhpcouchIAdapter The adapter instance.
	 *
	 * @author     David Zülke
	 * @since      1.0.0
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}
	
	/**
	 * Set an adapter to use with this connection.
	 *
	 * @param      PhpcouchIAdapter The adapter instance to use.
	 *
	 * @author     David Zülke
	 * @since      1.0.0
	 */
	public function setAdapter(PhpcouchIAdapter $adapter)
	{
		$this->adapter = $adapter;
	}
}

?>