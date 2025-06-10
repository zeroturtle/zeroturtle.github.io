<?php
require_once "session.php";
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST"  && isset($_POST['submit'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Now we check if the data from the login form was submitted, isset() will check if the data exists
    $error = '';
    // validate if email is empty
    if (!isset($_POST['email']) || empty($email)) {
        $error .= 'Please enter email';
    }

    // validate if password is empty
    if (!isset($_POST['password']) || empty($password)) {
        $error .= 'Please enter your password.';
    }

    if (empty($error)) {
      $res = $pdo->prepare('SELECT ID, USERNAME, PASSWORD FROM accounts WHERE active=true and email = ?');
      $res->execute([$email]);
      $user = $res->fetch();
      if($user && password_verify($password, $user['PASSWORD'])) {
          // Password is correct! User has logged in!
          // Regenerate the session ID to prevent session fixation attacks
          session_regenerate_id();
          // Declare session variables (they basically act like cookies but the data is remembered on the server)
          $_SESSION['account_loggedin'] = TRUE;
          $_SESSION['account_name'] = $user['USERNAME'];
          $_SESSION['account_id'] = $user['ID'];  // Store user ID in the session
          header('Location: '.$_SESSION['target_link']); 

//          header("Location: {$SERVER['HTTP_REFERRER'] == '' ? 'download.html' : 'licence.html'}");  // Redirect to the dashboard
          //echo 'Welcome back, ' . htmlspecialchars($_SESSION['account_name'], ENT_QUOTES) . '!';
          exit;
      }
      else {
        $error .= 'No User exist with that email address or incorrect password!';
      }
   }
   //echo '<p class="error">'.$error.'</p>';
}

require("login.html");
?>