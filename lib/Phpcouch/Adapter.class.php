<?php

abstract class PhpcouchAdapter
{
	protected $database;
	
	protected $url = '';
	
	public function __construct(array $options)
	{
		$options = array_merge(array('scheme' => 'http', 'host' => 'localhost', 'port' => '8888'), $options);
		
		if(!isset($options['database'])) {
			throw new PhpcouchException('No database configured');
		}
		
		$this->setDatabase($options['database']);
		
		$this->url = sprintf('%s://%s:%s/%s', $options['scheme'], $options['host'], $options['port'], $this->getDatabase());
	}
	
	protected function buildUri(array $options = array())
	{
		return $this->url . (isset($options['id']) ? '/' . $options['id'] : '');
	}
	
	public function getDatabase()
	{
		return $this->database;
	}
	
	public function setDatabase($database)
	{
		$this->database = $database;
	}
	
	abstract public function put($json, array $info = array(), array $options = array());
	
	abstract public function get(array $info, array $options = array());
	
	abstract public function post($json, array $info = array(), array $options = array());
	
	abstract public function delete(array $info, array $options = array());
}

?>