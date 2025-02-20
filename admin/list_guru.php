<?php
$title = "List Guru";
$active_page = "list_guru"; // Untuk menandai menu aktif di sidebar
include '../templates/header.php';
include '../templates/sidebar.php';

// Koneksi database
include '../includes/db.php';

// Proses Import Excel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['import_excel'])) {
    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['excel_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION));

        // Validasi format file
        if ($file_ext != 'xlsx' && $file_ext != 'xls') {
            echo "<script>alert('File harus berformat .xlsx atau .xls');</script>";
        } else {
            // Gunakan PhpSpreadsheet untuk membaca file Excel
            require_once '../vendor/autoload.php';

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_tmp);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                // Loop melalui baris data (mulai dari baris kedua untuk mengabaikan header)
                $header_skipped = false;
                foreach ($rows as $row) {
                    if (!$header_skipped) {
                        $header_skipped = true;
                        continue; // Lewati baris header
                    }

                    // Ambil data dari kolom Excel
                    $nama_guru = trim($row[0]);
                    $nip = trim($row[1]);
                    $jenis_kelamin = trim($row[2]);
                    $tanggal_lahir = trim($row[3]);
                    $alamat = trim($row[4]);

                    // Validasi data
                    if (!empty($nama_guru) && !empty($nip)) {
                        // Simpan data ke database
                        $stmt = $conn->prepare("INSERT INTO Guru (nama_guru, nip, jenis_kelamin, tanggal_lahir, alamat) VALUES (:nama_guru, :nip, :jenis_kelamin, :tanggal_lahir, :alamat)");
                        $stmt->bindParam(':nama_guru', $nama_guru);
                        $stmt->bindParam(':nip', $nip);
                        $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
                        $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
                        $stmt->bindParam(':alamat', $alamat);

                        if (!$stmt->execute()) {
                            echo "<script>alert('Gagal menyimpan data guru: " . implode(", ", $stmt->errorInfo()) . "');</script>";
                        }
                    } else {
                        echo "<script>alert('Data tidak lengkap di salah satu baris. Pastikan semua kolom wajib diisi.');</script>";
                    }
                }

                echo "<script>alert('Data guru berhasil diimpor dari Excel.');</script>";
            } catch (\Exception $e) {
                echo "<script>alert('Error saat membaca file Excel: " . htmlspecialchars($e->getMessage()) . "');</script>";
            }
        }
    } else {
        echo "<script>alert('File tidak valid atau gagal diunggah.');</script>";
    }
}

// Ambil data guru dari database
$stmt = $conn->query("SELECT * FROM Guru");
$guru_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">List Guru</h1>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Guru</h6>
                        </div>
                        <div class="card-header py-3">
                            <a href="tambah_guru.php" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Tambah Guru</a>
                            <!-- Form Upload Excel -->
                            <form method="POST" action="" enctype="multipart/form-data" style="display:inline;">
                                <input type="file" name="excel_file" accept=".xlsx, .xls" required>
                                <button type="submit" name="import_excel" class="btn btn-primary btn-sm"><i class="fas fa-file-import"></i> Import Excel</button>
                            </form>
                            <!-- Tombol Unduh Format Excel -->
                            <a href="../assets/format.xlsx" class="btn btn-info btn-sm" download><i class="fas fa-download"></i> Unduh Format Excel</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Guru</th>
                                        <th>NIP</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Alamat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($guru_list)): ?>
                                        <?php foreach ($guru_list as $guru): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($guru['nama_guru']); ?></td>
                                                <td><?php echo htmlspecialchars($guru['nip']); ?></td>
                                                <td><?php echo htmlspecialchars($guru['jenis_kelamin']); ?></td>
                                                <td><?php echo htmlspecialchars($guru['tanggal_lahir']); ?></td>
                                                <td><?php echo htmlspecialchars($guru['alamat']); ?></td>
                                                <td>
                                                    <a href="edit_guru.php?id=<?php echo htmlspecialchars($guru['id_guru']); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    <a href="hapus_guru.php?id=<?php echo htmlspecialchars($guru['id_guru']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data guru.</td>
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