<?php

class PhpcouchDatabase extends PhpcouchRecord
{
	public function __toString()
	{
		return $this->db_name;
	}
}

?>