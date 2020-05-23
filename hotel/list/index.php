<?php
require '../../config.php';
require '../../helper.php';
header('Content-type: application/json; charset=UTF-8');
$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

$list = array();
$statement = $db->prepare('SELECT hotels.id, chain_id, hotels.name, chains.name AS chain_name, grid_i,grid_j, created, user_agent, ip_address FROM hotels INNER JOIN chains on chain_id = chains.id ORDER by chain_id ASC;');
$result = $statement->execute();
while ($hotel = $result->fetchArray(SQLITE3_ASSOC)) {
  $list[] = $hotel;
}
$result->finalize();
$db->close();
echo json_encode(returnWrapper(0, $list), JSON_PRETTY_PRINT);
