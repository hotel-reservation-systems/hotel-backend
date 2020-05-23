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
if (empty(trim($_POST["reservation_id"]))) {
  echo json_encode(returnWrapper(91, null, "Missing reservation id"), JSON_PRETTY_PRINT);
  exit();
}

$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

// Validate reservation
$sql = "SELECT * FROM reservations WHERE id = " . trim($_POST["reservation_id"]) . ";";
$statement = $db->prepare($sql);
$result = $statement->execute();
$found_reservation = false;
while ($reservation = $result->fetchArray(SQLITE3_ASSOC)){
  if(!empty($reservation["id"])) {
    $found_reservation = true;
    if ($reservation["user_id"] != trim($_SESSION["id"])) {
      echo json_encode(returnWrapper(93, null, "You may not cancel other user\'s reservation"), JSON_PRETTY_PRINT);
      exit();
    }
  }
  else {
    echo json_encode(returnWrapper(1, null, "Runtime error"), JSON_PRETTY_PRINT);
    exit();
  }
}
$result->finalize();
if (!$found_reservation) {
  echo json_encode(returnWrapper(92, null, "Reservation does not exist"), JSON_PRETTY_PRINT);
  exit();
}

$sql = "SELECT * FROM reservations_canceled WHERE reservation_id = " . trim($_POST["reservation_id"]) . ";";
$statement = $db->prepare($sql);
$result = $statement->execute();
while ($reservations_canceled = $result->fetchArray(SQLITE3_ASSOC)){
  if(!empty($reservations_canceled["id"])) {
    echo json_encode(returnWrapper(94, null, "The reservation was already canceled"), JSON_PRETTY_PRINT);
    $result->finalize();
    exit();
  }
  else {
    echo json_encode(returnWrapper(1, null, "Runtime error"), JSON_PRETTY_PRINT);
    exit();
  }
}

// Prepare before inserting in database
$reservation_id = trim($_POST["reservation_id"]);
$user_id = trim($_SESSION["id"]);
$user_agent = SQLite3::escapeString(trim($_SERVER['HTTP_USER_AGENT']));
$ip_address = real_ip();

// Insert in database
$query_value = 'INSERT INTO reservations_canceled (reservation_id, user_id, created, user_agent, ip_address) VALUES (\'' . $reservation_id . '\',\'' . $user_id . '\',\'' . gmdate('Y-m-d H:i:s') . '\',\'' . $user_agent . '\',\'' . $ip_address . '\')';
$statement = $db->prepare($query_value);
$statement->execute();
echo json_encode(returnWrapper(0, null), JSON_PRETTY_PRINT);

$db->close();
