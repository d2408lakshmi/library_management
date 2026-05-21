<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['id'])){
    // Not logged in -> send to chooser
    header('location:index.php');
    exit();
}

$id_session = $_SESSION['id'];

// Enforce faculty-only access for pages that include this session file (admin area)
// If a Student is logged in and lands on an admin page, redirect them to the student dashboard
if (isset($_SESSION['user_type'])) {
    $type = $_SESSION['user_type'];
    if ($type !== 'Admin' && $type !== 'Librarian') {
        header('location: student_dashboard.php');
        exit();
    }
}
?>