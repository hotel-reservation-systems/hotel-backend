<?php
require '../../config.php';
require '../../helper.php';
header('Content-type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  echo json_encode(returnWrapper(4, null, "Wrong HTTP request method, " . $_SERVER['REQUEST_METHOD']. " has been used"), JSON_PRETTY_PRINT);
  exit;
}

session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  echo json_encode(returnWrapper(9, null, "A new user may not be created until the current user has logged out"), JSON_PRETTY_PRINT);
  exit;
}

$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

// Check if fields are empty
if (empty(trim($_POST["username"]))) {
  echo json_encode(returnWrapper(41, null, "Missing username"), JSON_PRETTY_PRINT);
  exit();
} elseif (empty(trim($_POST["password"]))) {
  echo json_encode(returnWrapper(42, null, "Missing password"), JSON_PRETTY_PRINT);
  exit();
} elseif (strlen(trim($_POST["password"])) < 6) {
  echo json_encode(returnWrapper(47, null, "Password must have at least 6 characters"), JSON_PRETTY_PRINT);
  exit();
} elseif (empty(trim($_POST["role"]))) {
  echo json_encode(returnWrapper(43, null, "Missing role"), JSON_PRETTY_PRINT);
  exit();
}

// Validate username
$sql = "SELECT id FROM \"users\" WHERE username = \"" . trim($_POST["username"]) . "\"";
$statement = $db->prepare($sql);
$result = $statement->execute();
while ($user = $result->fetchArray(SQLITE3_ASSOC)){
  if(!empty($user["id"])) {
    echo json_encode(returnWrapper(46, null, "This username is already taken"), JSON_PRETTY_PRINT);
    $result->finalize();
    $db->close();
    exit();
  }
  else {
    echo json_encode(returnWrapper(1, null, "Runtime error"), JSON_PRETTY_PRINT);
    exit();
  }
}

// Validate role
if (trim($_POST["role"]) != "customer" && trim($_POST["role"]) != "administrator") {
  echo json_encode(returnWrapper(47, null, "Illegal role"), JSON_PRETTY_PRINT);
  exit();
}

// Prepare before inserting in database
$username = trim($_POST["username"]);
$password = trim($_POST["password"]);
$role = trim($_POST["role"]);
$user_agent = SQLite3::escapeString(trim($_SERVER['HTTP_USER_AGENT']));
$ip_address = real_ip();

// Insert in database
$query_value = 'INSERT INTO users (username, password, role, created, user_agent, ip_address) VALUES (\'' . $username . '\',\'' . $password . '\',\'' . $role . '\',\'' . gmdate('Y-m-d H:i:s') . '\',\'' . $user_agent . '\',\'' . $ip_address . '\')';
// var_dump($query_value);
$statement = $db->prepare($query_value);
$statement->execute();
echo json_encode(returnWrapper(0, null), JSON_PRETTY_PRINT);
// $last_row_id = $db->lastInsertRowID();
//
// echo "The last inserted row Id is $last_row_id";
// var_dump($db->lastErrorMsg());
// Conclusion: chmod 777 both directory and the db file.
$db->close();
