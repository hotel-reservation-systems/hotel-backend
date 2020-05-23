<?php
require '../../config.php';
require '../../helper.php';
header('Content-type: application/json; charset=UTF-8');
$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

$list = array();
$sql = 'SELECT * FROM reservations WHERE id NOT in (SELECT reservation_id FROM reservations_canceled)';
$statement;
if (isset($_GET["user_id"])) {
  $statement = $db->prepare($sql . ' AND user_id = ' . trim($_GET["user_id"]) . ';');
}
else {
  $statement = $db->prepare($sql . ';');
}
$result = $statement->execute();
while ($reservation = $result->fetchArray(SQLITE3_ASSOC)) {
  $list[] = $reservation;
}
$result->finalize();
$db->close();
echo json_encode(returnWrapper(0, $list), JSON_PRETTY_PRINT);
