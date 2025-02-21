<?php
session_start(); // Start the session to access the token

// Destroy the session and redirect to the login page
session_destroy();
header('Location: login');
exit;
?>
