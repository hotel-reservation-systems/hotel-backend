<?php
require '../../config.php';
require '../../helper.php';
header('Content-type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  echo json_encode(returnWrapper(4, null, "Wrong HTTP request method, " . $_SERVER['REQUEST_METHOD']. " has been used"), JSON_PRETTY_PRINT);
  exit;
}

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
  echo json_encode(returnWrapper(5, null, "Logging in is required for this privileged action"), JSON_PRETTY_PRINT);
  exit;
}

if ($_SESSION["role"] !== "customer") {
  echo json_encode(returnWrapper(6, null, '"customer" role is required for this privileged action'), JSON_PRETTY_PRINT);
  exit;
}

// Check if parameters are empty
if (empty(trim($_POST["room_id"]))) {
  echo json_encode(returnWrapper(81, null, "Missing room_id"), JSON_PRETTY_PRINT);
  exit();
}
if (empty(trim($_POST["date_from"]))) {
  echo json_encode(returnWrapper(82, null, "Missing from date"), JSON_PRETTY_PRINT);
  exit();
}
if (empty(trim($_POST["date_to"]))) {
  echo json_encode(returnWrapper(83, null, "Missing to date"), JSON_PRETTY_PRINT);
  exit();
}
if (empty(trim($_POST["date_to"]))) {
  echo json_encode(returnWrapper(83, null, "Missing to date"), JSON_PRETTY_PRINT);
  exit();
}

// Validate date
$date_from = trim($_POST["date_from"]);
$date_to = trim($_POST["date_to"]);
$datetime1 = date_create($date_from);
$datetime2 = date_create($date_to);
$interval = date_diff($datetime1, $datetime2);
if ( ($interval->invert == 1) or ($interval->days == 0) ) {
  echo json_encode(returnWrapper(87, null, "date_to must be greater than date_from"), JSON_PRETTY_PRINT);
  exit();
}

$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

// Validate reservation
$sql = "SELECT * FROM reservations WHERE room_id = " . trim($_POST["room_id"]) . " AND id NOT in (SELECT reservation_id FROM reservations_canceled);";
$statement = $db->prepare($sql);
$result = $statement->execute();
$found_conflict = false;
while ($reservation = $result->fetchArray(SQLITE3_ASSOC)){
  if (date_diff(date_create($reservation["date_from"]), $datetime1)->invert == 1) {
    if ( (date_diff(date_create($reservation["date_from"]), $datetime2)->invert == 0) and (date_diff(date_create($reservation["date_from"]), $datetime2)->days != 0) ) {
      $found_conflict = true;
    }
  }
  else if (date_diff(date_create($reservation["date_to"]), $datetime1)->invert == 1) {
    $found_conflict = true;
  }
}
$result->finalize();
if ($found_conflict) {
  echo json_encode(returnWrapper(88, null, "Room is not available at requested date period"), JSON_PRETTY_PRINT);
  exit();
}

// Prepare before inserting in database
$user_id = trim($_SESSION["id"]);
$room_id = trim($_POST["room_id"]);
$user_agent = SQLite3::escapeString(trim($_SERVER['HTTP_USER_AGENT']));
$ip_address = real_ip();

// Insert in database
$query_value = 'INSERT INTO reservations (user_id, room_id, date_from, date_to, created, user_agent, ip_address) VALUES (\'' . $user_id . '\',\'' . $room_id . '\',\'' . $date_from . '\',\'' . $date_to . '\',\'' . gmdate('Y-m-d H:i:s') . '\',\'' . $user_agent . '\',\'' . $ip_address . '\')';
$statement = $db->prepare($query_value);
$statement->execute();
echo json_encode(returnWrapper(0, null), JSON_PRETTY_PRINT);

$db->close();
