<?php

namespace phpcouch;

use phpcouch\record;

class Database extends Record
{
	public function __toString()
	{
		return $this->db_name;
	}
}

?>