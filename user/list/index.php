<?php
require '../../config.php';
require '../../helper.php';
header('Content-type: application/json; charset=UTF-8');
$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

$list = array();
$statement = $db->prepare('SELECT * FROM "users" ORDER BY "id" ASC');
$result = $statement->execute();
while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
  $list[] = $user;
  // var_dump($user);
  // var_dump($list);
  // unset($user);
}
$result->finalize();
$db->close();
echo json_encode(returnWrapper(0, $list, "Please note that passwords are actually stored with encryption."), JSON_PRETTY_PRINT);
