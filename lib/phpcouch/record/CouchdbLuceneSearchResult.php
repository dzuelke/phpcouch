<?php

namespace phpcouch\record;

class CouchdbLuceneSearchResult extends ViewResult
{	
	public function hydrate($data)
	{
		//Couchdb-Lucene doesn't by default send content-type
		if ($data instanceOf \phpcouch\http\HttpResponse) {
			$data->setHeader('Content-type', 'application/json');
		}
		return parent::hydrate($data);
	}
}

?>