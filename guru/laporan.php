<?php
session_start();
include '../includes/db.php';

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
    SELECT asis.*, s.nama_siswa, k.nama_kelas
    FROM Absensi_Siswa asis
    JOIN Siswa s ON asis.id_siswa = s.id_siswa
    JOIN Kelas k ON s.id_kelas = k.id_kelas
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

// Tombol Download Laporan
if (isset($_GET['download']) && $_GET['download'] == 'csv') {
    // Header untuk file CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="laporan_absensi.csv"');

    // Buka output stream
    $output = fopen('php://output', 'w');

    // Tulis header kolom
    fputcsv($output, ['Tanggal', 'Nama Siswa', 'Kelas', 'Status Kehadiran', 'Jam Masuk', 'Jam Keluar', 'Catatan']);

    // Tulis data absensi
    foreach ($absensi_list as $absensi) {
        fputcsv($output, [
            $absensi['tanggal'],
            $absensi['nama_siswa'],
            $absensi['nama_kelas'],
            $absensi['status_kehadiran'],
            $absensi['jam_masuk'],
            $absensi['jam_keluar'],
            $absensi['catatan']
        ]);
    }

    // Tutup output stream
    fclose($output);
    exit;
}

if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
    require('../vendor/fpdf/fpdf.php'); // Pastikan path ke FPDF benar

    // Inisialisasi FPDF
    class PDF extends FPDF {
        // Kop Surat
        function Header() {
            // Logo sekolah (pastikan file logo.png ada di folder yang sama)
            $this->Image('../img/logo.png', 10, 10, 30); // Ukuran logo 30x30 px

            // Nama sekolah dan alamat
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'SMK Contoh Indonesia', 0, 1, 'C');
            $this->SetFont('Arial', '', 12);
            $this->Cell(0, 10, 'Jl. Contoh No. 123, Kota Contoh, Provinsi Contoh', 0, 1, 'C');
            $this->Ln(10);

            // Judul laporan
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, 'LAPORAN ABSENSI SISWA', 0, 1, 'C');
            $this->Ln(10);
        }

        // Footer
        function Footer() {
            $this->SetY(-15); // Posisi 15 mm dari bawah
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    // Inisialisasi objek PDF
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);

    // Filter tanggal (opsional)
    if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
        $pdf->Cell(0, 10, 'Periode: ' . $tanggal_awal . ' s/d ' . $tanggal_akhir, 0, 1, 'L');
        $pdf->Ln(5);
    }

    // Header tabel
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 10, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Nama Siswa', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Kelas', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Status', 1, 0, 'C');
    $pdf->Cell(20, 10, 'Jam Masuk', 1, 0, 'C');
    $pdf->Cell(20, 10, 'Jam Keluar', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Catatan', 1, 1, 'C');

    // Data absensi
    $pdf->SetFont('Arial', '', 10);
    foreach ($absensi_list as $absensi) {
        $pdf->Cell(30, 10, $absensi['tanggal'], 1, 0, 'C');
        $pdf->Cell(40, 10, $absensi['nama_siswa'], 1, 0, 'L');
        $pdf->Cell(30, 10, $absensi['nama_kelas'], 1, 0, 'C');
        $pdf->Cell(30, 10, $absensi['status_kehadiran'], 1, 0, 'C');
        $pdf->Cell(20, 10, $absensi['jam_masuk'], 1, 0, 'C');
        $pdf->Cell(20, 10, $absensi['jam_keluar'], 1, 0, 'C');
        $pdf->Cell(40, 10, $absensi['catatan'], 1, 1, 'L');
    }

    // Bersihkan buffer output
    ob_end_clean();

    // Output file PDF
    $pdf->Output('D', 'laporan_absensi.pdf'); // Gunakan 'D' untuk memaksa unduhan
    exit;
}
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
                                    <a href="?<?php echo http_build_query($_GET); ?>&download=csv" class="btn btn-success">Download CSV</a>
                                    <a href="?<?php echo http_build_query($_GET); ?>&download=pdf" class="btn btn-danger">Download PDF</a>
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