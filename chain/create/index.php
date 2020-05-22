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

$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

// Check if chain name is empty
if (empty(trim($_POST["name"]))) {
  echo json_encode(returnWrapper(51, null, "Missing chain name"), JSON_PRETTY_PRINT);
  exit();
}

// Validate chain name
$sql = "SELECT * FROM \"chains\" WHERE name = \"" . trim($_POST["name"]) . "\"";
$statement = $db->prepare($sql);
$result = $statement->execute();
while ($user = $result->fetchArray(SQLITE3_ASSOC)){
  if(!empty($user["name"])) {
    echo json_encode(returnWrapper(52, null, "This chain name is already taken"), JSON_PRETTY_PRINT);
    $result->finalize();
    $db->close();
    exit();
  }
  else {
    echo json_encode(returnWrapper(1, null, "Runtime error"), JSON_PRETTY_PRINT);
    exit();
  }
}

// Prepare before inserting in database
$chain_name = trim($_POST["name"]);
$user_id = $_SESSION["id"];

// Insert in database
$query_value = 'INSERT INTO chains (user_id, name) VALUES (\'' . $user_id . '\',\'' . $chain_name . '\')';
// var_dump($query_value);
$statement = $db->prepare($query_value);
$statement->execute();
echo json_encode(returnWrapper(0, null), JSON_PRETTY_PRINT);

$db->close();
