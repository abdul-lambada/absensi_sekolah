<?php
session_start();
include '../includes/db.php';
$active_page = "laporan_siswa"; // Untuk menandai menu aktif di sidebar

// Periksa apakah sesi 'user' tersedia
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Filter berdasarkan tanggal
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';
$id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : '';

// Query untuk mengambil daftar kelas
$stmt_kelas = $conn->prepare("SELECT * FROM Kelas");
$stmt_kelas->execute();
$kelas_list = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data absensi
$query = "
    SELECT 
        asis.tanggal,
        u.name AS nama_siswa,
        k.nama_kelas,
        asis.status AS status_kehadiran,
        asis.catatan,
        kh.timestamp AS waktu_kehadiran,
        kh.status AS status_verifikasi
    FROM Absensi_Siswa asis
    JOIN Siswa s ON asis.id_siswa = s.id_siswa
    JOIN Kelas k ON s.id_kelas = k.id_kelas
    JOIN users u ON s.user_id = u.id
    LEFT JOIN tbl_kehadiran kh ON kh.user_id = u.id
    WHERE 1=1
";

$params = [];
if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $query .= " AND asis.tanggal BETWEEN :tanggal_awal AND :tanggal_akhir";
    $params[':tanggal_awal'] = $tanggal_awal;
    $params[':tanggal_akhir'] = $tanggal_akhir;
}
if (!empty($id_kelas)) {
    $query .= " AND k.id_kelas = :id_kelas";
    $params[':id_kelas'] = $id_kelas;
}

$query .= " ORDER BY asis.tanggal DESC";

$stmt_absensi = $conn->prepare($query);
$stmt_absensi->execute($params);
$absensi_list = $stmt_absensi->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Laporan Absensi - Management Salassika</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">
    <?php include '../templates/header.php'; ?>
    <?php include '../templates/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <h1 class="h3 mb-0 text-gray-800">Laporan Absensi</h1>
            </nav>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Filter Laporan Absensi</h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="">
                                    <!-- Filter Tanggal -->
                                    <label>Tanggal Awal:</label>
                                    <input type="date" name="tanggal_awal" class="form-control" value="<?php echo htmlspecialchars($tanggal_awal); ?>"><br>

                                    <label>Tanggal Akhir:</label>
                                    <input type="date" name="tanggal_akhir" class="form-control" value="<?php echo htmlspecialchars($tanggal_akhir); ?>"><br>

                                    <!-- Filter Kelas -->
                                    <label>Kelas:</label>
                                    <select name="id_kelas" class="form-control">
                                        <option value="">-- Semua Kelas --</option>
                                        <?php foreach ($kelas_list as $kelas): ?>
                                            <option value="<?php echo htmlspecialchars($kelas['id_kelas']); ?>" 
                                                <?php echo ($id_kelas == $kelas['id_kelas']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($kelas['nama_kelas']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select><br>

                                    <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Laporan Absensi -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Laporan Absensi</h6>
                            </div>
                            <div class="card-body">
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
                                                <td colspan="7" class="text-center">Tidak ada data absensi.</td>
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