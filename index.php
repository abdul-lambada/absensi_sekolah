<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}

// Redirect berdasarkan role
if ($_SESSION['user']['role'] === 'admin') {
    header("Location: admin/index.php");
} elseif ($_SESSION['user']['role'] === 'guru') {
    header("Location: guru/index.php");
}
?>