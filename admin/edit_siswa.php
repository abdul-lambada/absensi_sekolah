<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id_siswa = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM Siswa WHERE id_siswa = :id_siswa");
$stmt->bindParam(':id_siswa', $id_siswa);
$stmt->execute();
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nisn = $_POST['nisn'];
    $nama_siswa = $_POST['nama_siswa'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $id_kelas = $_POST['id_kelas'];

    $stmt = $conn->prepare("UPDATE Siswa SET nisn = :nisn, nama_siswa = :nama_siswa, jenis_kelamin = :jenis_kelamin, tanggal_lahir = :tanggal_lahir, alamat = :alamat, id_kelas = :id_kelas WHERE id_siswa = :id_siswa");
    $stmt->bindParam(':nisn', $nisn);
    $stmt->bindParam(':nama_siswa', $nama_siswa);
    $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
    $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
    $stmt->bindParam(':alamat', $alamat);
    $stmt->bindParam(':id_kelas', $id_kelas);
    $stmt->bindParam(':id_siswa', $id_siswa);
    $stmt->execute();

    if ($stmt->execute()) {
        // Redirect ke halaman list guru dengan status success
        header("Location: list_siswa.php?status=edit_success");
        exit();
    } else {
        // Redirect ke halaman list guru dengan status error
        header("Location: list_siswa.php?status=edit_success");
        exit();
    }
}

// Ambil daftar kelas untuk dropdown
$stmt_kelas = $conn->query("SELECT * FROM Kelas");
$kelas_list = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Siswa - Management Salassika</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin-2.css" rel="stylesheet">
</head>

<body id="page-top">
    <?php include '../templates/header.php'; ?>
    <?php include '../templates/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <h1 class="h3 mb-0 text-gray-800">Edit Siswa</h1>
            </nav>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Form Edit Siswa</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <label>NISN:</label>
                                    <input type="text" name="nisn" class="form-control" value="<?php echo $siswa['nisn']; ?>" required><br>
                                    <label>Nama Siswa:</label>
                                    <input type="text" name="nama_siswa" class="form-control" value="<?php echo $siswa['nama_siswa']; ?>" required><br>
                                    <label>Jenis Kelamin:</label>
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="Laki-laki" <?php echo ($siswa['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="Perempuan" <?php echo ($siswa['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                    </select><br>
                                    <label>Tanggal Lahir:</label>
                                    <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $siswa['tanggal_lahir']; ?>" required><br>
                                    <label>Alamat:</label>
                                    <textarea name="alamat" class="form-control" required><?php echo $siswa['alamat']; ?></textarea><br>
                                    <label>Kelas:</label>
                                    <select name="id_kelas" class="form-control">
                                        <?php foreach ($kelas_list as $kelas): ?>
                                            <option value="<?php echo $kelas['id_kelas']; ?>" <?php echo ($siswa['id_kelas'] == $kelas['id_kelas']) ? 'selected' : ''; ?>>
                                                <?php echo $kelas['nama_kelas']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select><br>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../templates/footer.php'; ?>
</body>

</html>