<?php

set_include_path(get_include_path() . ':' . '/Users/dzuelke/Downloads/ZendFramework-1.0.2/library');

require('../lib/Phpcouch.php');

PhpCouch::registerConnection('default', $con = new PhpcouchConnection(array('database' => 'hellohans'), new PhpcouchZendhttpclientAdapter()));

$doc = $con->retrieve('63A0B00A68EEBE4ECB4E0F8F9682F813');

$doc = $con->newDocument();
$doc->_id = uniqid();
$doc->type = 'Page';
$doc->title = 'Hello world again!';
$doc->content = 'Something more';
var_dump($doc, $doc->toArray(), $doc->dehydrate());

$doc->save();

$doc->title .= 'Snap';
$doc->save();

$doc = $con->newDocument();
$doc->type = 'Page';
$doc->title = 'An unnamed document';
$doc->content = 'Yay zomg! :>>';
$doc->save();
var_dump($doc);

?>