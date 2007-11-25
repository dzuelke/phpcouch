<?php

error_reporting(E_ALL | E_STRICT);

set_include_path(get_include_path() . ':' . '/Users/dzuelke/Downloads/ZendFramework-1.0.2/library');

require('../lib/Phpcouch.php');

PhpCouch::registerConnection('default', $con = new PhpcouchDatabaseConnection(array('database' => 'hellohans'), new PhpcouchZendhttpclientAdapter()));

PhpCouch::registerConnection('server', $con2 = new PhpcouchServerConnection(array(), new PhpcouchZendhttpclientAdapter()), false);

var_dump($con2->listDatabases());

var_dump($con2->retrieveDatabase('hellohans'));
try {
	var_dump($con2->createDatabase('hellohans2'));
	var_dump($con2->deleteDatabase('hellohans2'));
} catch(PhpcouchClientErrorException $e) {
}

$doc = $con->retrieveDocument('63A0B00A68EEBE4ECB4E0F8F9682F813');
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