<?php

class PhpcouchDatabase
{
	protected $_data = array();
	
	public function __construct(PhpcouchConnection $connection = null)
	{
		if($connection === null) {
			$connection = Phpcouch::getConnection();
		}
		
		$this->_connection = $connection;
	}
}

?>