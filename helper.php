<?php
function returnWrapper($code = 0, $data = null, $message = "success") {
  $timestamp = date("Y-m-d h:m:s T+Z");
  $wrapper = array("code"=>$code, "data"=>$data, "message"=>$message, "timestamp"=>$timestamp);
  return $wrapper;
}

function real_ip() {
  $ip_address = null;
  // whether ip is from share internet
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
  }
  // whether ip is from proxy
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  // whether ip is from remote address
  else {
    $ip_address = $_SERVER['REMOTE_ADDR'];
  }
  return $ip_address;
}
