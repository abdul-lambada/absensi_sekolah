<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id_jurusan = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM Jurusan WHERE id_jurusan = :id_jurusan");
$stmt->bindParam(':id_jurusan', $id_jurusan);
$stmt->execute();

header("Location: list_jurusan.php");
exit;
?>