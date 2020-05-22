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
  echo json_encode(returnWrapper(8, null, "A new user may not be logged in until the current user has logged out"), JSON_PRETTY_PRINT);
  exit;
}

// Check if fields are empty
if (empty(trim($_POST["username"]))) {
  echo json_encode(returnWrapper(41, null, "Missing username"), JSON_PRETTY_PRINT);
  exit();
} elseif (empty(trim($_POST["password"]))) {
  echo json_encode(returnWrapper(42, null, "Missing password"), JSON_PRETTY_PRINT);
  exit();
}

$db = new SQLite3(SQLITE3_LOCATION, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

// Validate username and password
$sql = "SELECT id, username, password, role FROM \"users\" WHERE username = \"" . trim($_POST["username"]) . "\"";
$statement = $db->prepare($sql);
$result = $statement->execute();
while ($user = $result->fetchArray(SQLITE3_ASSOC)){
  if(!empty($user["id"])) {
    if($user["password"] == trim($_POST["password"])) {
      // Password is correct
      // Store data in session variables
      $_SESSION["loggedin"] = true;
      $_SESSION["id"] = $user["id"];
      $_SESSION["username"] = $user["username"];
      $_SESSION["role"] = $user["role"];

      echo json_encode(returnWrapper(0, null), JSON_PRETTY_PRINT);
      $result->finalize();
      $db->close();
      exit();
    }
    else {
      // Display an error message if password is not valid
      echo json_encode(returnWrapper(44, null, "Invalid password"), JSON_PRETTY_PRINT);
      $result->finalize();
      $db->close();
      exit();
    }
  }
  else {
    echo json_encode(returnWrapper(1, null, "Runtime error"), JSON_PRETTY_PRINT);
    $result->finalize();
    $db->close();
    exit();
  }
}
echo json_encode(returnWrapper(45, null, "No account found with this username"), JSON_PRETTY_PRINT);
$result->finalize();
$db->close();
exit();
