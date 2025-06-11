<?php
  // Start the session
  session_start();
  // Destroy the active session, which logs the user out 
  session_destroy();
  // Redirect to the welcone page
  header('Location: about.html');
  exit;
?>