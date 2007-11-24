<?php

interface PhpcouchIDocument
{
	public function getAttachments();
	public function hasAttachments();
	public function retrieveAttachment($name);
	public function retrieveRevision($revision);
}

?>