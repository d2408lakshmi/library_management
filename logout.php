<?php
// Your session.php file should already contain session_start()
include('session.php');

// Step 1: Unset all of the session variables.
// This function takes NO arguments.
session_unset();

// Step 2: Destroy the session completely.
session_destroy();

// Step 3: Redirect the user to the login page.
header('location: index.php');

// Step 4: Add exit() to ensure no more code is run after the redirect.
exit();
?>