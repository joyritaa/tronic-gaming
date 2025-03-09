<?php
// logout.php
session_start();   // Start or resume the current session
session_destroy(); // Destroy all session data

// Redirect the user to the login page
header("Location: login.html");
exit();
?>
