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

// Check if chain name is empty
if (empty(trim($_POST["name"]))) {
  echo json_encode(returnWrapper(54, null, "Missing hotel name"), JSON_PRETTY_PRINT);
  exit();
}
if (empty(trim($_POST["grid_i"]))) {
  echo json_encode(returnWrapper(56, null, "Missing grid_i"), JSON_PRETTY_PRINT);
  exit();
}
if (trim($_POST["grid_i"]) > 100 || trim($_POST["grid_i"]) < 0) {
  echo json_encode(returnWrapper(58, null, "Invalid grid_i"), JSON_PRETTY_PRINT);
  exit();
}
if (empty(trim($_POST["grid_j"]))) {
  echo json_encode(returnWrapper(57, null, "Missing grid_j"), JSON_PRETTY_PRINT);
  exit();
}
if (trim($_POST["grid_j"]) > 100 || trim($_POST["grid_j"]) < 0) {
  echo json_encode(returnWrapper(59, null, "Invalid grid_j"), JSON_PRETTY_PRINT);
  exit();
}

$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

// Validate hotel name
$sql = "SELECT * FROM \"hotels\" WHERE name = \"" . trim($_POST["name"]) . "\"";
$statement = $db->prepare($sql);
$result = $statement->execute();
while ($hotel = $result->fetchArray(SQLITE3_ASSOC)){
  if(!empty($hotel["name"])) {
    echo json_encode(returnWrapper(55, null, "This hotel name is already taken"), JSON_PRETTY_PRINT);
    $result->finalize();
    $db->close();
    exit();
  }
  else {
    echo json_encode(returnWrapper(1, null, "Runtime error"), JSON_PRETTY_PRINT);
    exit();
  }
}

// Validate hotel grid
$sql = "SELECT * FROM \"hotels\" WHERE grid_i = \"" . trim($_POST["grid_i"]) . "\" AND grid_j = \"" . trim($_POST["grid_j"]) . "\"";
$statement = $db->prepare($sql);
$result = $statement->execute();
while ($hotel = $result->fetchArray(SQLITE3_ASSOC)){
  if(!empty($hotel["id"])) {
    echo json_encode(returnWrapper(60, null, "There already exists one hotel at the same grid(i,j)"), JSON_PRETTY_PRINT);
    $result->finalize();
    $db->close();
    exit();
  }
  else {
    echo json_encode(returnWrapper(1, null, "Runtime error"), JSON_PRETTY_PRINT);
    exit();
  }
}

// Get chain_id of the current administrator
$sql = "SELECT * FROM \"chains\" WHERE user_id = \"" . trim($_SESSION["id"]) . "\"";
$statement = $db->prepare($sql);
$result = $statement->execute();
$chain_id = "";
while ($chain = $result->fetchArray(SQLITE3_ASSOC)){
  if(!empty($chain["id"])) {
    $chain_id = $chain["id"];
  }
  else {
    echo json_encode(returnWrapper(1, null, "Runtime error"), JSON_PRETTY_PRINT);
    exit();
  }
}
$result->finalize();

// Prepare before inserting in database
$name = trim($_POST["name"]);
$grid_i = trim($_POST["grid_i"]);
$grid_j = trim($_POST["grid_j"]);
$user_agent = SQLite3::escapeString(trim($_SERVER['HTTP_USER_AGENT']));
$ip_address = real_ip();

// Insert in database
$query_value = 'INSERT INTO hotels (chain_id, name, grid_i, grid_j, created, user_agent, ip_address) VALUES (\'' . $chain_id . '\',\'' . $name . '\',\'' . $grid_i . '\',\'' . $grid_j . '\',\'' . gmdate('Y-m-d H:i:s') . '\',\'' . $user_agent . '\',\'' . $ip_address . '\')';
$statement = $db->prepare($query_value);
$statement->execute();
echo json_encode(returnWrapper(0, null), JSON_PRETTY_PRINT);

$db->close();
