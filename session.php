<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['id'])){
header('location:index.php');
}
$id_session=$_SESSION['id'];
?>