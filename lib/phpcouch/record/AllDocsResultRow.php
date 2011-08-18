<?php

namespace phpcouch\record;

class AllDocsResultRow extends DesignDocumentResultRow
{
	const DEFAULT_ACCESSOR = 'doc';
	
	public function getDocument($accessor = null)
	{
		if(isset($this->doc)) {
			$retval = new Document($this->getDesignDocumentResult()->getDatabase());
			$retval->hydrate($this->doc);
		} else {
			$retval = $this->getViewResult()->getDatabase()->retrieveDocument($this->id);
		}
		
		return $retval;
	}
}

?>