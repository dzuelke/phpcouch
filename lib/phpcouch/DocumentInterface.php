<?php

namespace phpcouch;

interface DocumentInterface
{
	public function getAttachments();
	public function hasAttachments();
	public function retrieveAttachment($name);
}

?>