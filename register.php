<?php

require_once "session.php";
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
/*
contains at least 8 characters
metcontains both lower (a-z) and upper case letters (A-Z)
metcontains at least one number (0-9) or a symbol
metdoes not contain your email address
metis not commonly used
*/
    $fullname = trim($_POST["name"]);
    $email = trim($_POST["email"]); //stripslashes
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirm_password"]);
    $newsLetter = isset($_POST["newsletter"]);

    $error = "";
    // Validate inputs
    if(empty($fullname)){
        $error .= "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($fullname))){
        $error .= "Username can only contain letters, numbers, and underscores.";
    } 
    // Validate password
    if(empty($password)){
        $error .= "Please enter a password.";     
    } elseif(strlen($password) < 8){
        $error .= "Password must have atleast 6 characters.";
    }
    // Check if passwords match
    // Validate confirm password
    if(empty($confirmPassword)){
       $error .= "Please confirm password.";     
    } elseif(empty($error) && ($password != $confirmPassword)){
       $error .= "Password did not match.";
    }

    if(empty($email)){
        $error .= "Email address is a mandatory field.";
    }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= "Invalid email address format!";
    } else {
        // Check if email are unique
      $row = $pdo->prepare("SELECT id FROM ACCOUNTS WHERE email = ?");
      $row->execute([$email]);
      $user = $row->fetch();
      if($user){
        $error .= "This email is already taken.";
      }
    }

    // Check input errors before inserting in database
    if(empty($error)){
      try {
        // Insert data into the database
        $pdo->prepare("INSERT INTO ACCOUNTS (username, password, email, newsletter) VALUES (?, ?, ?, ?)")->execute([$fullname, password_hash($password, PASSWORD_DEFAULT), $email, $newsLetter]);
        header("location: login.php");
        exit;
      }
      catch(PDOException $e) { echo $e->getMessage(); }
    }
    //echo '<p class="text-danger">'.$error.'</p>';
}

require("register.html");
?>