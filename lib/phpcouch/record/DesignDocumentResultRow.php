<?php

namespace phpcouch\record;

class DesignDocumentResultRow extends Record implements DesignDocumentResultRowInterface
{
	const DEFAULT_ACCESSOR = null;
	
	protected $designDocumentResult;
	
	public function __construct(DesignDocumentResultInterface $designDocumentResult = null)
	{
		parent::__construct($designDocumentResult->getDatabase()->getConnection());
		
		$this->designDocumentResult = $designDocumentResult;
	}
	
	public function getDesignDocumentResult()
	{
		return $this->designDocumentResult;
	}
	
	public function getDocument($accessor = null)
	{
		if($accessor === null) {
			$accessor = static::DEFAULT_ACCESSOR;
		}
		
		if($accessor === null) {
			// the value contains the document itself
			$doc = $this->value;
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
		
		$retval = new Document($this->getDesignDocumentResult()->getDatabase());
		$retval->hydrate($doc);
		
		return $retval;
	}
}

?>