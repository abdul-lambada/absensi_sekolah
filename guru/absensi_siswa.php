<?php
session_start();
include '../includes/db.php';

// Periksa apakah sesi 'user' tersedia
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header("Location: ../auth/login.php");
    exit;
}

$active_page = "absensi_siswa"; // Untuk menandai menu aktif di sidebar

try {
    // Ambil daftar kelas
    $stmt_kelas = $conn->prepare("SELECT * FROM Kelas");
    $stmt_kelas->execute();
    $kelas_list = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);

    // Jika form absensi disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_absensi'])) {
        $tanggal = date('Y-m-d');
        $id_kelas = $_POST['id_kelas'];

        foreach ($_POST['status'] as $id_siswa => $status_kehadiran) {
            // Cek apakah absensi sudah ada untuk hari ini
            $stmt_check = $conn->prepare("SELECT * FROM Absensi_Siswa WHERE id_siswa = :id_siswa AND tanggal = :tanggal");
            $stmt_check->bindParam(':id_siswa', $id_siswa);
            $stmt_check->bindParam(':tanggal', $tanggal);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {
                continue; // Lewati jika absensi sudah ada
            }

            // Ambil data dari form
            $catatan = filter_var($_POST['catatan'][$id_siswa], FILTER_SANITIZE_STRING);

            // Simpan absensi baru untuk tiap siswa
            $stmt_insert = $conn->prepare("
                INSERT INTO Absensi_Siswa (id_siswa, tanggal, status, catatan)
                VALUES (:id_siswa, :tanggal, :status_kehadiran, :catatan)
            ");
            $stmt_insert->bindParam(':id_siswa', $id_siswa);
            $stmt_insert->bindParam(':tanggal', $tanggal);
            $stmt_insert->bindParam(':status_kehadiran', $status_kehadiran);
            $stmt_insert->bindParam(':catatan', $catatan);
            $stmt_insert->execute();
        }

        echo "<script>alert('Absensi berhasil disimpan.');</script>";
    }

    // Jika kelas dipilih melalui GET
    $id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : null;
    $siswa_list = array(); // Menggunakan array() untuk kompatibilitas PHP 5.3

    if ($id_kelas) {
        $stmt_siswa = $conn->prepare("
    SELECT 
        s.id_siswa,
        u.name AS nama_siswa, -- Menggunakan 'u.name' sebagai pengganti 's.nisn'
        s.jenis_kelamin,
        s.tanggal_lahir,
        s.alamat,
        s.nis
    FROM Siswa s
    JOIN users u ON s.user_id = u.id 
    WHERE s.id_kelas = :id_kelas
");
        $stmt_siswa->bindParam(':id_kelas', $id_kelas);
        $stmt_siswa->execute();
        $siswa_list = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil riwayat absensi
    $stmt_history = $conn->prepare("
    SELECT 
        asis.id_absensi_siswa AS id_absensi,
        asis.tanggal,
        u.name AS nama_siswa,
        k.nama_kelas,
        asis.status AS status_kehadiran,
        asis.catatan,
        kh.timestamp AS waktu_kehadiran, -- Ambil timestamp dari tbl_kehadiran
        kh.status AS status_verifikasi   -- Ambil status dari tbl_kehadiran
    FROM Absensi_Siswa asis
    JOIN Siswa s ON asis.id_siswa = s.id_siswa
    JOIN Kelas k ON s.id_kelas = k.id_kelas
    JOIN users u ON s.user_id = u.id
    LEFT JOIN tbl_kehadiran kh ON kh.user_id = u.id -- Tambahkan join ke tbl_kehadiran
    ORDER BY asis.tanggal DESC
");

    $stmt_history->execute();
    $absensi_list = $stmt_history->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                <!-- filepath: c:\xampp\htdocs\absensi_sekolah\guru\absensi_siswa.php -->
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
                                                <th>Jenis Kelamin</th>
                                                <th>Tanggal Lahir</th>
                                                <th>Alamat</th>
                                                <th>NIS</th>
                                                <th>Waktu Kehadiran</th>
                                                <th>Status Kehadiran</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($siswa_list as $siswa): ?>
                                                <?php
                                                // Ambil data kehadiran dari tbl_kehadiran berdasarkan user_id
                                                $stmt_kehadiran = $conn->prepare("
                                    SELECT timestamp, status 
                                    FROM tbl_kehadiran 
                                    WHERE user_id = :user_id 
                                    ORDER BY timestamp DESC 
                                    LIMIT 1
                                ");
                                                $stmt_kehadiran->bindParam(':user_id', $siswa['id_siswa'], PDO::PARAM_INT);
                                                $stmt_kehadiran->execute();
                                                $kehadiran = $stmt_kehadiran->fetch(PDO::FETCH_ASSOC);
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($siswa['nama_siswa']); ?></td>
                                                    <td><?php echo htmlspecialchars($siswa['jenis_kelamin']); ?></td>
                                                    <td><?php echo htmlspecialchars($siswa['tanggal_lahir']); ?></td>
                                                    <td><?php echo htmlspecialchars($siswa['alamat']); ?></td>
                                                    <td><?php echo htmlspecialchars($siswa['nis']); ?></td>
                                                    <td><?php echo htmlspecialchars(isset($kehadiran['timestamp']) ? $kehadiran['timestamp'] : 'Belum Ada'); ?></td>
                                                    <td>
                                                        <select name="status[<?php echo $siswa['id_siswa']; ?>]" class="form-control">
                                                            <option value="Hadir" <?php echo (isset($kehadiran['status']) && $kehadiran['status'] === 'Masuk') ? 'selected' : ''; ?>>Hadir</option>
                                                            <option value="Tidak Hadir" <?php echo (isset($kehadiran['status']) && $kehadiran['status'] === 'Keluar') ? 'selected' : ''; ?>>Tidak Hadir</option>
                                                            <option value="Sakit">Sakit</option>
                                                            <option value="Ijin">Ijin</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="catatan[<?php echo $siswa['id_siswa']; ?>]" class="form-control" placeholder="Catatan">
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
                <!-- filepath: c:\xampp\htdocs\absensi_sekolah\guru\absensi_siswa.php -->
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
                                                <th>Catatan</th>
                                                <th>Waktu Kehadiran</th>
                                                <th>Status Verifikasi</th>
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
                                                        <td><?php echo htmlspecialchars($absensi['catatan']); ?></td>
                                                        <td><?php echo htmlspecialchars(isset($absensi['waktu_kehadiran']) ? $absensi['waktu_kehadiran'] : 'Belum Ada'); ?></td>
                                                        <td><?php echo htmlspecialchars(isset($absensi['status_verifikasi']) ? $absensi['status_verifikasi'] : 'Belum Diverifikasi'); ?></td>
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