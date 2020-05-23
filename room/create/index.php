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

if ($_SESSION["role"] !== "administrator") {
  echo json_encode(returnWrapper(7, null, '"administrator" role is required for this privileged action'), JSON_PRETTY_PRINT);
  exit;
}

// Check if parameters are empty
if (empty(trim($_POST["hotel_id"]))) {
  echo json_encode(returnWrapper(71, null, "Missing hotel_id"), JSON_PRETTY_PRINT);
  exit();
}
if (empty(trim($_POST["name"]))) {
  echo json_encode(returnWrapper(72, null, "Missing room name"), JSON_PRETTY_PRINT);
  exit();
}
if (empty(trim($_POST["description"]))) {
  echo json_encode(returnWrapper(80, null, "Missing description"), JSON_PRETTY_PRINT);
  exit();
}
if (empty(trim($_POST["rate"]))) {
  echo json_encode(returnWrapper(73, null, "Missing rate"), JSON_PRETTY_PRINT);
  exit();
}
if (empty(trim($_POST["price"]))) {
  echo json_encode(returnWrapper(74, null, "Missing price"), JSON_PRETTY_PRINT);
  exit();
}

// Logical check
if (!in_array(trim($_POST["rate"]), array("cheap", "moderate", "deluxe"))) {
  echo json_encode(returnWrapper(75, null, "Rate must be one of: ['cheap', 'moderate', 'deluxe']"), JSON_PRETTY_PRINT);
  exit();
}
if (($_POST["price"] < 0)) {
  echo json_encode(returnWrapper(76, null, "Price must be greater or equal to zero"), JSON_PRETTY_PRINT);
  exit();
}

$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

// Validate hotel_id
$sql = "SELECT id FROM hotels WHERE hotels.chain_id = (SELECT id FROM chains WHERE user_id = " . $_SESSION["id"] . ");";
$statement = $db->prepare($sql);
$result = $statement->execute();
$found_hotel = false;
while ($hotel = $result->fetchArray(SQLITE3_ASSOC)){
  if ($hotel["id"] == trim($_POST["hotel_id"])) {
    $found_hotel = true;
  }
}
$result->finalize();
if (!$found_hotel) {
  echo json_encode(returnWrapper(77, null, "hotel_id does not exist or user does not own this hotel_id"), JSON_PRETTY_PRINT);
  exit();
}

// Validate room name
$sql = "SELECT * FROM \"rooms\" WHERE name = \"" . trim($_POST["name"]) . "\" AND hotel_id = \"" . trim($_POST["hotel_id"]) . "\"";
$statement = $db->prepare($sql);
$result = $statement->execute();
while ($room = $result->fetchArray(SQLITE3_ASSOC)){
  if(!empty($room["name"])) {
    echo json_encode(returnWrapper(78, null, "This room name is already taken"), JSON_PRETTY_PRINT);
    $result->finalize();
    $db->close();
    exit();
  }
  else {
    echo json_encode(returnWrapper(1, null, "Runtime error"), JSON_PRETTY_PRINT);
    exit();
  }
}

// Validate rate
if (trim($_POST["rate"]) != "moderate") {
  $sql = "SELECT id FROM rooms WHERE hotel_id = " . trim($_POST["hotel_id"]) . " AND rate = \"" . trim($_POST["rate"]) . "\";";
  $statement = $db->prepare($sql);
  $result = $statement->execute();
  $hotel_count = 0;
  while ($hotel = $result->fetchArray(SQLITE3_ASSOC)){
    // var_dump($hotel);
    $hotel_count++;
  }
  // die();
  $result->finalize();
  if ($hotel_count > 2) {
    echo json_encode(returnWrapper(77, null, "A hotel may not have more than two " . trim($_POST["rate"]) . " rooms"), JSON_PRETTY_PRINT);
    exit();
  }
}

// Prepare before inserting in database
$hotel_id = trim($_POST["hotel_id"]);
$name = trim($_POST["name"]);
$description = trim($_POST["description"]);
$rate = trim($_POST["rate"]);
$price = trim($_POST["price"]);
$user_agent = SQLite3::escapeString(trim($_SERVER['HTTP_USER_AGENT']));
$ip_address = real_ip();

// Insert in database
$query_value = 'INSERT INTO rooms (hotel_id, name, description, rate, price, created, user_agent, ip_address) VALUES (\'' . $hotel_id . '\',\'' . $name . '\',\'' . $description . '\',\'' . $rate . '\',\'' . $price . '\',\'' . gmdate('Y-m-d H:i:s') . '\',\'' . $user_agent . '\',\'' . $ip_address . '\')';
$statement = $db->prepare($query_value);
$statement->execute();
echo json_encode(returnWrapper(0, null), JSON_PRETTY_PRINT);

$db->close();
