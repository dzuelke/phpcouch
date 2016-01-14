<?php

namespace phpcouch\record;

class ViewResultRow extends Record implements ViewResultRowInterface
{
	const DEFAULT_ACCESSOR = null;
	
	/**
	 * @var ViewResultInterface
	 */
	protected $viewResult;
	
	/**
	 * @param ViewResultInterface $viewResult
	 */
	public function __construct(ViewResultInterface $viewResult = null)
	{
		parent::__construct($viewResult->getDatabase()->getConnection());
		
		$this->viewResult = $viewResult;
	}
	
	/**
	 * @return ViewResultInterface
	 */
	public function getViewResult()
	{
		return $this->viewResult;
	}

	/**
	 * @param mixed $accessor
	 * @param bool  $retrieve
	 * @return Document|null
	 */
	public function getDocument($accessor = null, $retrieve = false)
	{
		if($accessor === null) {
			$accessor = static::DEFAULT_ACCESSOR;
		}
		
		if($accessor === null) {
			// the value contains the document itself
			$doc = $this->value;
			// if the view didn't emit the actual doc as value but was called with include_docs=true
			if((isset($this->doc) && $this->doc instanceof \StdClass) || !$doc || (!isset($doc->_id) && !isset($doc['_id']))) {
				$doc = $this->doc;
			}
		} elseif(is_callable($accessor)) {
			// an anonymous function or another kind of callback that will grab the value for us
			$doc = call_user_func($accessor, $this);
		} elseif(is_array($this->value) && isset($this->value[$accessor])) {
			// value is an array
			$doc = $this->value[$accessor];
		} elseif(isset($this->value->$accessor)) {
			// it's the name of a property
			$doc = $this->value->$accessor;
		} else {
			// exception
		}
		
		if($doc) {
			$retval = new Document($this->getViewResult()->getDatabase());
			$retval->hydrate($doc);
			
			return $retval;
		} elseif($retrieve) {
			// the view didn't emit the actual doc as value and the view wasn't called with include_docs=true
			return $this->viewResult->getDatabase()->retrieveDocument($this->id);
		} else {
			return null;
		}
	}
}

?>