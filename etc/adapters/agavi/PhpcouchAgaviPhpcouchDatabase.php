<?php
/**
 * You will need to add the following parameters to databases.xml
 * - hostname
 * - port
 *
 * Example:
 *		<databases default="Phpcouch">
 *			<database name="Phpcouch" class="AgaviPhpcouchDatabase">
 *				<ae:parameter name="hostname">localhost</ae:parameter>
 *				<ae:parameter name="port">5984</ae:parameter>
 *			</database>
 *		</databases>
 */

class PhpcouchAgaviPhpcouchDatabase extends AgaviDatabase
{
	/**
	 * @var        phpcouch\record\Database The database instance associated with this connection (if configured).
	 */
	protected $database;
	
	/**
	 * Connect to the database
	 *
	 * @throws     AgaviDatabaseException If a connection could not be established
	 *
	 * @author     Simon Thulbourn <simon.thulbourn@bitextender.com>
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 */
	protected function connect()
	{
		if($this->hasParameter('adapter') && $this->hasParameter('adapter[class]')) {
			$cls = $this->getParameter('adapter[class]');
			if($this->hasParameter('adapter[arguments]')) {
				// we have ctor arguments for our adapter
				// must use reflection for this
				$rcls = new ReflectionClass($cls);
				$rcls->newInstanceArgs($this->getParameter('adapter[arguments]'));
			} else {
				$adapter = new $cls();
			}
		} else {
			$adapter = null;
		}
		
		$con = new \phpcouch\connection\Connection($this->getParameter('uri'), $adapter);
		
		\phpcouch\Phpcouch::registerConnection(
			$this->getParameter('name', $this->getName()),
			$con,
			$this->getParameter('default', true)
		);
	
		$this->connection = $this->resource = $con;
		
		if($this->hasParameter('database')) {
			$this->database = $this->getConnection()->retrieveDatabase($this->getParameter('database'));
		}
	}
	
	/**
	 * Fetch the database associated with this connection, if configured.
	 *
	 * @return     phpcouch\record\Database The database instance.
	 * 
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 */
	public function getDatabase()
	{
		$this->getConnection();
		
		return $this->database;
	}
	
	/**
	 * Shutdown method.
	 * 
	 * @author     David Zülke <david.zuelke@bitextender.com>
	 */
	public function shutdown()
	{
		unset($this->connection, $this->resource, $this->database);
	}
	
	/**
	 * Initialize this Database.
	 *
	 * @param      AgaviDatabaseManager The database manager of this instance.
	 * @param      array                An assoc array of initialization params.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 */
	public function initialize(AgaviDatabaseManager $databaseManager, array $parameters = array())
	{
		parent::initialize($databaseManager, $parameters);
		
		if(!class_exists('phpcouch\Phpcouch')) {
			// assume it's on the include path
			include('phpcouch/Phpcouch.php');
		}
		
		// call bootstrap regardless. it won't re-initialize if it's already been done
		\phpcouch\Phpcouch::bootstrap();
		
		if(!$this->hasParameter('database') && ($database = trim(parse_url($this->getParameter('uri'), PHP_URL_PATH), '/'))) {
			$this->setParameter('database', $database);
		}
	}
}

?>