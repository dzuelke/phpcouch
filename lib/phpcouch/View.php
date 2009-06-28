<?php

namespace phpcouch;

class View
{
	protected $_data = array();
	
	public function __construct(phpcouch\connection\Connection $connection = null)
	{
		if($connection === null) {
			$connection = phpcouch\Phpcouch::getConnection();
		}
		
		$this->_connection = $connection;
	}
}

?>