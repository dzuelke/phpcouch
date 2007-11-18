<?php

interface PhpcouchIDocument extends PhpcouchIMutableRecord
{
	public function retrieveAttachment($name);
	public function listAttachments();
	public function retrieveRevision($revision);
}

?>