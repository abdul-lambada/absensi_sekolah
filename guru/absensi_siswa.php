<?php
session_start();
include '../includes/db.php';

// Periksa apakah sesi 'user' tersedia
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil semua data siswa beserta nama kelas
$stmt_siswa_list = $conn->prepare("
    SELECT s.id_siswa, s.nama_siswa, k.nama_kelas
    FROM Siswa s
    JOIN Kelas k ON s.id_kelas = k.id_kelas
");
$stmt_siswa_list->execute();
$siswa_list = $stmt_siswa_list->fetchAll(PDO::FETCH_ASSOC);

// Simpan absensi jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['id_siswa']) || empty($_POST['id_siswa'])) {
        die("Error: Silakan pilih siswa.");
    }

    $id_siswa = $_POST['id_siswa'];
    $tanggal = date('Y-m-d');
    $status_kehadiran = $_POST['status_kehadiran'];
    $jam_masuk = $_POST['jam_masuk'];
    $jam_keluar = $_POST['jam_keluar'];
    $catatan = $_POST['catatan'];

    // Cek apakah sudah ada absensi hari ini untuk siswa yang dipilih
    $stmt_check = $conn->prepare("SELECT * FROM Absensi_Siswa WHERE id_siswa = :id_siswa AND tanggal = :tanggal");
    $stmt_check->bindParam(':id_siswa', $id_siswa);
    $stmt_check->bindParam(':tanggal', $tanggal);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        echo "<script>alert('Absensi untuk siswa ini sudah ada hari ini.');</script>";
    } else {
        // Simpan absensi baru
        $stmt_insert = $conn->prepare("
            INSERT INTO Absensi_Siswa (id_siswa, tanggal, status_kehadiran, jam_masuk, jam_keluar, catatan)
            VALUES (:id_siswa, :tanggal, :status_kehadiran, :jam_masuk, :jam_keluar, :catatan)
        ");
        $stmt_insert->bindParam(':id_siswa', $id_siswa);
        $stmt_insert->bindParam(':tanggal', $tanggal);
        $stmt_insert->bindParam(':status_kehadiran', $status_kehadiran);
        $stmt_insert->bindParam(':jam_masuk', $jam_masuk);
        $stmt_insert->bindParam(':jam_keluar', $jam_keluar);
        $stmt_insert->bindParam(':catatan', $catatan);

        if ($stmt_insert->execute()) {
            echo "<script>alert('Absensi berhasil disimpan.');</script>";
        } else {
            echo "<script>alert('Gagal menyimpan absensi.');</script>";
        }
    }
}

// Ambil riwayat absensi siswa
$stmt_history = $conn->prepare("
    SELECT asis.*, s.nama_siswa, k.nama_kelas
    FROM Absensi_Siswa asis
    JOIN Siswa s ON asis.id_siswa = s.id_siswa
    JOIN Kelas k ON s.id_kelas = k.id_kelas
    ORDER BY asis.tanggal DESC
");
$stmt_history->execute();
$absensi_list = $stmt_history->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Absensi Siswa - Absensi Sekolah</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">
    <?php include '../templates/header.php'; ?>
    <?php include '../templates/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <h1 class="h3 mb-0 text-gray-800">Absensi Siswa</h1>
            </nav>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Form Absensi Siswa</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <!-- Dropdown untuk Memilih Siswa -->
                                    <label>Pilih Siswa:</label>
                                    <select name="id_siswa" class="form-control" required>
                                        <option value="">-- Pilih Siswa --</option>
                                        <?php foreach ($siswa_list as $siswa): ?>
                                            <option value="<?php echo htmlspecialchars($siswa['id_siswa']); ?>">
                                                <?php echo htmlspecialchars($siswa['nama_siswa']) . ' (' . htmlspecialchars($siswa['nama_kelas']) . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select><br>

                                    <!-- Input Absensi -->
                                    <label>Status Kehadiran:</label>
                                    <select name="status_kehadiran" class="form-control" required>
                                        <option value="Hadir">Hadir</option>
                                        <option value="Izin">Izin</option>
                                        <option value="Sakit">Sakit</option>
                                    </select><br>

                                    <label>Jam Masuk:</label>
                                    <input type="time" name="jam_masuk" class="form-control" required><br>

                                    <label>Jam Keluar:</label>
                                    <input type="time" name="jam_keluar" class="form-control" required><br>

                                    <label>Catatan (Opsional):</label>
                                    <textarea name="catatan" class="form-control"></textarea><br>

                                    <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Absensi -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Riwayat Absensi</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th>Status Kehadiran</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($absensi_list)): ?>
                                            <?php foreach ($absensi_list as $absensi): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($absensi['tanggal']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['nama_siswa']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['nama_kelas']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['status_kehadiran']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['jam_masuk']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['jam_keluar']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['catatan']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada riwayat absensi.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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