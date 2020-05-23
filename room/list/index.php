<?php
require '../../config.php';
require '../../helper.php';
header('Content-type: application/json; charset=UTF-8');
$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

$list = array();
$statement;
if (isset($_GET["hotel_id"])) {
  $statement = $db->prepare('SELECT rooms.id, hotel_id, rooms.name AS name, hotels.name AS hotel_name, rate, price, rooms.created, rooms.user_agent, rooms.ip_address FROM rooms INNER JOIN hotels on hotel_id = hotels.id WHERE hotel_id = ' . trim($_GET["hotel_id"]) . ' ORDER BY hotel_id ASC, hotels.id ASC;');
}
else {
  $statement = $db->prepare('SELECT rooms.id, hotel_id, rooms.name AS name, hotels.name AS hotel_name, rate, price, rooms.created, rooms.user_agent, rooms.ip_address FROM rooms INNER JOIN hotels on hotel_id = hotels.id ORDER BY hotel_id ASC, hotels.id ASC;');
}
$result = $statement->execute();
while ($room = $result->fetchArray(SQLITE3_ASSOC)) {
  $list[] = $room;
}
$result->finalize();
$db->close();
echo json_encode(returnWrapper(0, $list), JSON_PRETTY_PRINT);
