<?php
  // Start the session
  session_start();
  // Destroy the active session, which logs the user out
 
  // Unset all of the session variables
  $_SESSION = array();

  session_destroy();
  // Redirect to the login pag
  header('Location: about.html');
  exit;
?>