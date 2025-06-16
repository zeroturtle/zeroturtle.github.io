<?php
//if ($_SERVER['REFERER'] != '') exit;

require_once "session.php";
require_once "config.php";

if (!isset($_GET['code'])) {
	exit("can't find the page"); 
}

$error = "";
$code = $_GET['code']; 
$stmt = $pdo->prepare("SELECT * FROM resetPasswords WHERE code = ?");
$stmt->execute([$code]);
$user = $stmt->fetch();
if (!$user) {
  $err .= ("<p>can't find the page because not same code</p>"); 
}
else {
  //
  $email = $user['email'];  
  $pdo->prepare("UPDATE accounts SET active = 1 WHERE email = ?")->execute([$email]);
  $pdo->prepare("DELETE FROM resetPasswords WHERE code = ?")->execute([$code]);
  header("location: login.html");
  exit;
}


require("login.html");
?>