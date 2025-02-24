<?php
session_start();
include '../includes/db.php';

// Periksa apakah sesi 'user' tersedia
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil daftar kelas
$stmt_kelas = $conn->prepare("SELECT * FROM Kelas");
$stmt_kelas->execute();
$kelas_list = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);

// Jika form absensi disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_absensi'])) {
    $tanggal = date('Y-m-d');
    $id_kelas = $_POST['id_kelas'];
    // Loop tiap siswa yang dikirim dari form
    foreach ($_POST['status'] as $id_siswa => $status_kehadiran) {
        // Cek apakah absensi sudah ada untuk hari ini
        $stmt_check = $conn->prepare("SELECT * FROM Absensi_Siswa WHERE id_siswa = :id_siswa AND tanggal = :tanggal");
        $stmt_check->bindParam(':id_siswa', $id_siswa);
        $stmt_check->bindParam(':tanggal', $tanggal);
        $stmt_check->execute();
        if ($stmt_check->rowCount() > 0) {
            // Jika sudah ada, lewati atau dapat tambahkan log/update
            continue;
        } 
        // Ambil data dari form
        $jam_masuk = $_POST['jam_masuk'][$id_siswa];
        $jam_keluar = $_POST['jam_keluar'][$id_siswa];
        $catatan = $_POST['catatan'][$id_siswa];
        
        // Simpan absensi baru untuk tiap siswa
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
        $stmt_insert->execute();
    }
    echo "<script>alert('Absensi berhasil disimpan.');</script>";
}

// Jika kelas telah dipilih melalui GET
$id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : null;
$siswa_list = [];  // Renamed variable

if ($id_kelas) {
    $stmt_siswa = $conn->prepare("SELECT id_siswa, nisn, nama_siswa, jenis_kelamin, tanggal_lahir, alamat, nis FROM Siswa WHERE id_kelas = :id_kelas");
    $stmt_siswa->bindParam(':id_kelas', $id_kelas);
    $stmt_siswa->execute();
    $siswa_list = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Absensi Siswa - Management Salassika</title>
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
                <!-- Form pemilihan kelas -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Pilih Kelas</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <label>Pilih Kelas:</label>
                            <select name="id_kelas" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas_list as $kelas): ?>
                                    <option value="<?php echo htmlspecialchars($kelas['id_kelas']); ?>" <?php if ($id_kelas == $kelas['id_kelas']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($kelas['nama_kelas']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <br>
                            <button type="submit" class="btn btn-secondary">Tampilkan Siswa</button>
                        </form>
                    </div>
                </div>
                <!-- Form Absensi Siswa -->
                <?php if ($id_kelas && !empty($siswa_list)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Form Absensi Siswa</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="id_kelas" value="<?php echo htmlspecialchars($id_kelas); ?>">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Siswa</th>
                                            <th>Hadir</th>
                                            <th>Alpa</th>
                                            <th>Sakit</th>
                                            <th>Telat</th>
                                            <th>Ijin</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($siswa_list as $siswa): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($siswa['nama_siswa']); ?></td>
                                            <td><input type="radio" name="status[<?php echo $siswa['id_siswa']; ?>]" value="Hadir" required></td>
                                            <td><input type="radio" name="status[<?php echo $siswa['id_siswa']; ?>]" value="Alpa"></td>
                                            <td><input type="radio" name="status[<?php echo $siswa['id_siswa']; ?>]" value="Sakit"></td>
                                            <td><input type="radio" name="status[<?php echo $siswa['id_siswa']; ?>]" value="Telat"></td>
                                            <td><input type="radio" name="status[<?php echo $siswa['id_siswa']; ?>]" value="Ijin"></td>
                                            <td>
                                                <input type="date" name="jam_masuk[<?php echo $siswa['id_siswa']; ?>]" value="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="date" name="jam_keluar[<?php echo $siswa['id_siswa']; ?>]" value="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                                            </td>
                                            <td>
                                                <textarea name="catatan[<?php echo $siswa['id_siswa']; ?>]" class="form-control"></textarea>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" name="submit_absensi" class="btn btn-primary">Simpan Absensi</button>
                        </form>
                    </div>
                </div>
                <?php elseif ($id_kelas): ?>
                    <p>Tidak ada data siswa untuk kelas ini.</p>
                <?php endif; ?>
                
                <!-- Riwayat Absensi -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Riwayat Absensi</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
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
    </div>
    <?php include '../templates/footer.php'; ?>
</body>
</html>