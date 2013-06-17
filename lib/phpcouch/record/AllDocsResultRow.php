<?php

namespace phpcouch\record;

class AllDocsResultRow extends ViewResultRow
{
	const DEFAULT_ACCESSOR = 'doc';
	
	/**
	 * @param mixed $accessor
	 * @param bool  $retrieve
	 * @return Document|null
	 */
	public function getDocument($accessor = null, $retrieve = false)
	{
		if(isset($this->doc)) {
			$retval = new Document($this->getViewResult()->getDatabase());
			$retval->hydrate($this->doc);
		} elseif($retrieve) {
			$retval = $this->getViewResult()->getDatabase()->retrieveDocument($this->id);
		} else {
			$retval = null;
		}
		
		return $retval;
	}
}

?>