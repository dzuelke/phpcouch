<?php

namespace phpcouch\record;

class ShowResult extends Record
{
	protected $database;
	
	/**
	 * Class constructor
	 *
	 * @param      \phpcouch\record\Database database instance
	 *
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function __construct(Database $database = null)
	{
		parent::__construct($database->getConnection());
		
		$this->database = $database;
	}
	
	/**
	 * Get Database
	 *
	 * @return     \phpcouch\record\Database
	 *
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function getDatabase()
	{
		return $this->database;
	}
	
	/**
	 * Hydrates the class
	 *
	 * @param      mixed data
	 *
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function hydrate($data)
	{
		if(!$data instanceof \phpcouch\http\HttpResponse) {
			$this->data = $data->getContent();
		}
		
		$contentType = $data->getContentType();
		if(preg_match('#^application/json#', $contentType)) {
			// we want to suppress any errors
			$json = @json_decode($data->getContent(), true);
			
			// check for errors and fallback to plaintext if needed
			if(json_last_error() == JSON_ERROR_NONE) {
				$this->data = $json;
			} else {
				$this->data = $data->getContent();
			}
		} elseif(preg_match('#^(text/html|application/xml)#', $contentType)) {
			// suppress errors again...
			$xml = @simplexml_load_string($data->getContent());
			
			if($xml) {
				$this->data = $xml;
			} else {
				$this->data = $data->getContent();
			}
		} else {
			$this->data = $data->getContent();
		}
	}
	
	/**
	 * Get content
	 *
	 * @return     mixed
	 *
	 * @author     Simon Thulbourn <simon+github@thulbourn.com>
	 */
	public function getContent()
	{
		return $this->data;
	}
}