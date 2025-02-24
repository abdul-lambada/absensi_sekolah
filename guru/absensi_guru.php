<?php
session_start();
include '../includes/db.php';

// Periksa apakah sesi 'user' tersedia
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil semua data guru untuk dropdown
$stmt_guru_list = $conn->prepare("SELECT id_guru, nama_guru, nip, jenis_kelamin FROM Guru");
$stmt_guru_list->execute();
$guru_list = $stmt_guru_list->fetchAll(PDO::FETCH_ASSOC);

// Simpan absensi jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_guru = $_POST['id_guru']; // Ambil id_guru dari dropdown
    $tanggal = date('Y-m-d');
    $status_kehadiran = $_POST['status_kehadiran'];
    $jam_masuk = $_POST['jam_masuk'];
    $jam_keluar = $_POST['jam_keluar'];
    $catatan = $_POST['catatan'];

    // Cek apakah sudah ada absensi hari ini untuk guru yang dipilih
    $stmt_check = $conn->prepare("SELECT * FROM Absensi_Guru WHERE id_guru = :id_guru AND tanggal = :tanggal");
    $stmt_check->bindParam(':id_guru', $id_guru);
    $stmt_check->bindParam(':tanggal', $tanggal);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        echo "<script>alert('Absensi untuk guru ini sudah ada hari ini.');</script>";
    } else {
        // Simpan absensi baru
        $stmt_insert = $conn->prepare("
            INSERT INTO Absensi_Guru (id_guru, tanggal, status_kehadiran, jam_masuk, jam_keluar, catatan)
            VALUES (:id_guru, :tanggal, :status_kehadiran, :jam_masuk, :jam_keluar, :catatan)
        ");
        $stmt_insert->bindParam(':id_guru', $id_guru);
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

// Ambil riwayat absensi guru
$stmt_history = $conn->prepare("
    SELECT ag.*, g.nama_guru, g.nip, g.jenis_kelamin
    FROM Absensi_Guru ag
    JOIN Guru g ON ag.id_guru = g.id_guru
    ORDER BY ag.tanggal DESC
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
    <title>Absensi Guru - Management Salassika</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">
    <?php include '../templates/header.php'; ?>
    <?php include '../templates/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <h1 class="h3 mb-0 text-gray-800">Absensi Guru</h1>
            </nav>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Form Absensi Guru</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <!-- Dropdown untuk Memilih Guru -->
                                    <label>Pilih Guru:</label>
                                    <select name="id_guru" class="form-control" required>
                                        <option value="">-- Pilih Guru --</option>
                                        <?php foreach ($guru_list as $guru): ?>
                                            <option value="<?php echo htmlspecialchars($guru['id_guru']); ?>">
                                                <?php echo htmlspecialchars($guru['nama_guru']) . ' (' . htmlspecialchars($guru['nip']) . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select><br>

                                    <!-- Input Absensi -->
                                    <label>Status Kehadiran:</label>
                                    <select name="status_kehadiran" class="form-control" required>
                                        <option value="Hadir">Hadir</option>
                                        <option value="Telat">Telat</option>
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
                                            <th>Nama Guru</th>
                                            <th>NIP</th>
                                            <th>Jenis Kelamin</th>
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
                                                    <td><?php echo htmlspecialchars($absensi['nama_guru']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['nip']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['jenis_kelamin']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['status_kehadiran']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['jam_masuk']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['jam_keluar']); ?></td>
                                                    <td><?php echo htmlspecialchars($absensi['catatan']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada riwayat absensi.</td>
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