<?php
require '../../config.php';
require '../../helper.php';
header('Content-type: application/json; charset=UTF-8');
$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

if (empty(trim($_GET["date_from"]))) {
  echo json_encode(returnWrapper(82, null, "Missing from date"), JSON_PRETTY_PRINT);
  exit();
}
if (empty(trim($_GET["date_to"]))) {
  echo json_encode(returnWrapper(83, null, "Missing to date"), JSON_PRETTY_PRINT);
  exit();
}

$list = array();
$sql = 'SELECT hotel_id, rooms.id AS room_id, hotels.name AS hotel_name, rooms.name AS room_name, description, rate, price FROM rooms INNER JOIN hotels ON hotels.id = rooms.hotel_id WHERE rooms.id NOT in (SELECT DISTINCT room_id FROM reservations WHERE rooms.id NOT in (SELECT reservation_id FROM reservations_canceled) and (date_to <= \'' . trim($_POST["date_from"]) . '\' or date_from >= \'' . trim($_POST["date_to"]) . '\'))';
$statement;

if (isset($_GET["rate"]) and !empty($_GET["rate"])) {
  $sql = $sql . ' AND rate = \'' . trim($_GET["rate"]) . '\'';
}
if (isset($_GET["price_from"]) and !empty($_GET["price_from"])) {
  $sql = $sql . ' AND price >= ' . trim($_GET["price_from"]);
}
if (isset($_GET["price_to"]) and !empty($_GET["price_to"])) {
  $sql = $sql . ' AND price <= ' . trim($_GET["price_to"]);
}
if (isset($_GET["hotel_name"]) and !empty($_GET["hotel_name"])) {
  $sql = $sql . ' AND hotel_name LIKE \'%' . trim($_GET["hotel_name"]) . '%\'';
}
if (isset($_GET["order_by"]) and !empty($_GET["order_by"])) {
  $sql = $sql . ' ORDER BY ' . trim($_GET["order_by"]) . ' ASC';
}

$statement = $db->prepare($sql . ';');
$result = $statement->execute();
while ($reservation = $result->fetchArray(SQLITE3_ASSOC)) {
  $list[] = $reservation;
}
$result->finalize();
$db->close();
echo json_encode(returnWrapper(0, $list), JSON_PRETTY_PRINT);
