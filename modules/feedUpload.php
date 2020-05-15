<?php
require(__DIR__."/../config.php");
# Redirect invalid request
# var_dump($_SERVER);
# feedUploadError:
if ( !($_SERVER['REQUEST_METHOD'] == 'POST') ) {
  # 1 - Wrong Request Method
  header("location:" . $site_url . "?authuser=0&page=home&feedUploadError=1");
}
else if ( !isset($_POST["authuser"]) || ($_POST["authuser"] != 0 && $_POST["authuser"] != 1) ) {
  # 2 - Authuser not Specified
  header("location:" . $site_url . "?authuser=0&page=home&feedUploadError=2");
}
else if ( !isset($_POST["mainInput"]) || empty(trim($_POST["mainInput"])) ) {
  # 3 - Empty Input
  header("location:" . $site_url . "?authuser=" . $_POST["authuser"] . "&page=home&feedUploadError=3");
}
else {
  $sqlite3_filename = __DIR__."/../" . $sqlite3_filename;
  $content = $_POST["mainInput"];
  $authuser = $_POST["authuser"];
  //whether ip is from share internet
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
  }
  //whether ip is from proxy
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  //whether ip is from remote address
  else {
    $ip_address = $_SERVER['REMOTE_ADDR'];
  }
  $user_agent = $_SERVER['HTTP_USER_AGENT'];

  # Open the database connection
  $db = new SQLite3($sqlite3_filename, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
  $query_value = "INSERT INTO \"posts\" (\"content\", \"authuser\", \"created\", \"ip_address\", \"user_agent\") VALUES (\"" . $content . "\", \"" . $authuser . "\", \"" . gmdate('Y-m-d H:i:s') . "\", \"" . $ip_address . "\", \"" . SQLite3::escapeString($user_agent) . "\")";
  # Insert the post to database
  $db->exec('BEGIN');
  $db->query($query_value);
  # REMEMBER TO SET UP SQLite WRITE (e.g. 777) PERMISSION PROPERLY!!!
  $db->exec('COMMIT');

  header("location:" . $site_url . "?authuser=" . $authuser . "&page=home");
}
?>
