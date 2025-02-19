<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id_siswa = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM Siswa WHERE id_siswa = :id_siswa");
$stmt->bindParam(':id_siswa', $id_siswa);
$stmt->execute();

header("Location: list_siswa.php");
exit;
?>