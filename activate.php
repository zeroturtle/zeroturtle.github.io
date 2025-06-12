<?php

require_once "session.php";
require_once "config.php";

if (!isset($_GET['code'])) {
	exit("can't find the page"); 
}

$error = "";
$code = $_GET['code']; 
$row = $pdo->prepare("SELECT * FROM resetPasswords WHERE code = ?")->execute([$code])->fetch();
if (!$row) {
  $err .= ("<p>can't find the page because not same code</p>"); 
}
else {
  //
  $email = $row['EMAIL']; 
  $pdo->prepare("UPDATE users SET activate = 1 WHERE email = ?")->execute([$email]);
  $pdo->prepare("DELETE FROM resetPasswords WHERE code = ?")->execute([$code]);
  header("location: login.html");
  exit;
}


require("login.html");
?>