<?php
// активация нового аккаунта
// пускать только если пришли с конкретного адреса
//if ($_SERVER['REFERER'] != '') exit;

require_once "session.php";
require_once "config.php";

$error = "";
if (!isset($_GET['code'])) {
  $error.="can't find the page"; 
}

$code = $_GET['code']; 
$stmt = $pdo->prepare("SELECT * FROM resetPasswords WHERE code = ?");
$stmt->execute([$code]);
$user = $stmt->fetch();
if (!$user) {
  $error.="<p>can't find the page because not same code</p>"; 
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