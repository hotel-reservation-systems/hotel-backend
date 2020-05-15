<?php
require '../../config.php';
require '../../helper.php';
header('Content-type: application/json; charset=UTF-8');
session_start();

$session = array('session_deployed' => isset($_SESSION) ? true : false);
$session += $_SESSION;
echo json_encode(returnWrapper(0, $session, null), JSON_PRETTY_PRINT);
