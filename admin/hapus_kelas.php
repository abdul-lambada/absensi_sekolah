<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id_kelas = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM Kelas WHERE id_kelas = :id_kelas");
$stmt->bindParam(':id_kelas', $id_kelas);
$stmt->execute();

header("Location: list_kelas.php");
exit;
?>