<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /Bankly_V2/login.php'); 
    exit();
}

$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? '';
$role     = $_SESSION['role'] ?? '';
