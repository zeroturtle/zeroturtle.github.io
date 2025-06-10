<?php
// Start the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["account_loggedin"]) && $_SESSION["account_loggedin"] === true){
    header("location: about.html");
    exit;
}

?>
