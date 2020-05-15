<?php
require '../../config.php';
require '../../helper.php';
header('Content-type: application/json; charset=UTF-8');

$sqlite = array('pdo_sqlite' => "enabled");
if (extension_loaded('pdo_sqlite') && extension_loaded('sqlite3')) $sqlite["pdo_sqlite"] = "enabled";
else $sqlite["pdo_sqlite"] = "undefined";
$sqlite += array('path' => realpath(SQLITE3_LOCATION));
$sqlite += array('readable' => is_readable(SQLITE3_LOCATION) ? true : false);
$sqlite += array('writable' => is_writable(SQLITE3_LOCATION) ? true : false);
$sqlite += array('executable' => is_executable(SQLITE3_LOCATION) ? true : false);
echo json_encode(returnWrapper(0, $sqlite), JSON_PRETTY_PRINT);
