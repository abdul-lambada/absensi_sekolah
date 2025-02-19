<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id_guru = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM Guru WHERE id_guru = :id_guru");
$stmt->bindParam(':id_guru', $id_guru);
$stmt->execute();

header("Location: index.php");
exit;
?>