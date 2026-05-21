<?php
// --- Redirect to Login Chooser ---
// This ensures users are directed to the dual-login system

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['id'])) {
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'Student') {
        header("location: student_dashboard.php");
    } else {
        header("location: home.php");
    }
    exit();
}

// Redirect to login chooser
header("location: login_chooser.php");
exit();
?>