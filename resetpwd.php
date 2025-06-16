<?php 
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
  $email = $user['email']; 
}

// handling the form 
if (isset($_POST['password'])) {
  if ($row['EXPDATE'] < date("Y-m-d H:i:s")){
    $error .= "<h2>Link Expired</h2>
        <p>The link is invalid/expired. You are trying to use the expired link which 
        as valid only 24 hours (1 days after request) or you have already used the key in which case it is 
        deactivated.<br /><br /></p>";
  } else {
    if ($_POST["password"] != $_POST["confirm_password"]){
      $error .= "<p>Password do not match, both password should be same.<br /><br /></p>";
    } else {
	try {
          $pw = password_hash($_POST['password'], PASSWORD_DEFAULT);
  	  $pdo->prepare("UPDATE accounts SET password = ? WHERE email = ?")->execute([$pw, $email]);
          $pdo->prepare("DELETE FROM resetPasswords WHERE code = ?")->execute([$code]);
          $error .= '<div class="success"><p>Your password has been updated successfully!</p>
                     <p><a href="/login.html">Click here</a> to Login.</p></div><br />';
  	}
        catch {
          $error .= '<p>Something went wrong :(</p>';
 	}
    }
  }
}

require("resetpwd.html");
?>