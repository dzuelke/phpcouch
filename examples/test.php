<?php

error_reporting(E_ALL | E_STRICT);

set_include_path(get_include_path() . ':' . '/Users/dzuelke/Downloads/ZendFramework-1.0.2/library');

require('../lib/Phpcouch.php');

PhpCouch::registerConnection('default', $con = new PhpcouchConnection(array('database' => 'hellohans'), new PhpcouchZendhttpclientAdapter()));

var_dump($con->retrieveDatabase('hellohans'));
try {
	var_dump($con->createDatabase('hellohans2'));
	var_dump($con->deleteDatabase('hellohans2'));
} catch(PhpcouchClientErrorException $e) {
}

$doc = $con->retrieve('63A0B00A68EEBE4ECB4E0F8F9682F813');
$doc->title = 'hello again';
$doc->save();

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