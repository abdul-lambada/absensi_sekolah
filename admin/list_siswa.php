<?php
$title = "List Siswa";
$active_page = "list_siswa"; // Untuk menandai menu aktif di sidebar
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

                // Nonaktifkan foreign key checks
                $conn->exec("SET FOREIGN_KEY_CHECKS = 0;");

                // Mulai transaksi
                $conn->beginTransaction();

                // Hapus semua data siswa sebelum import
                $conn->exec("TRUNCATE TABLE Siswa");

                // Loop melalui baris data
                $header_skipped = false;
                foreach ($rows as $index => $row) {
                    if (!$header_skipped) {
                        $header_skipped = true;
                        continue; // Lewati baris header
                    }

                    // Ambil data dari kolom Excel
                    $nama_siswa = trim($row[0]);
                    $nisn = trim($row[1]);
                    $jenis_kelamin = trim($row[2]);
                    $tanggal_lahir = trim($row[3]);
                    $alamat = trim($row[4]);
                    $id_kelas = trim($row[5]);

                    // Validasi data
                    if (!empty($nama_siswa) && !empty($nisn) && !empty($jenis_kelamin) && !empty($tanggal_lahir) && !empty($id_kelas)) {
                        // Jika alamat kosong, isi dengan string kosong
                        if (empty($alamat)) {
                            $alamat = "";
                        }

                        // Simpan data ke database
                        $stmt = $conn->prepare("INSERT INTO Siswa (nama_siswa, nisn, jenis_kelamin, tanggal_lahir, alamat, id_kelas) VALUES (:nama_siswa, :nisn, :jenis_kelamin, :tanggal_lahir, :alamat, :id_kelas)");
                        $stmt->bindParam(':nama_siswa', $nama_siswa);
                        $stmt->bindParam(':nisn', $nisn);
                        $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
                        $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
                        $stmt->bindParam(':alamat', $alamat);
                        $stmt->bindParam(':id_kelas', $id_kelas);

                        if (!$stmt->execute()) {
                            throw new \Exception("Gagal menyimpan data siswa di baris ke-" . ($index + 1));
                        }
                    } else {
                        throw new \Exception("Data tidak lengkap di baris ke-" . ($index + 1));
                    }
                }

                // Commit transaksi jika sukses
                $conn->commit();
                echo "<script>alert('Semua data siswa berhasil disimpan.');</script>";
            } catch (\Exception $e) {
                // Rollback transaksi jika terjadi error
                $conn->rollBack();
                echo "<script>alert('Error saat menyimpan data: " . htmlspecialchars($e->getMessage()) . "');</script>";
            } finally {
                // Aktifkan kembali foreign key checks
                $conn->exec("SET FOREIGN_KEY_CHECKS = 1;");
            }
        }
    } else {
        echo "<script>alert('File tidak valid atau gagal diunggah.');</script>";
    }
}

// Ambil data siswa dari database
$stmt = $conn->query("SELECT s.id_siswa, s.nama_siswa, s.nisn, s.jenis_kelamin, s.tanggal_lahir, s.alamat, k.nama_kelas 
                      FROM Siswa s 
                      LEFT JOIN Kelas k ON s.id_kelas = k.id_kelas");
$siswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">List Siswa</h1>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Siswa</h6>
                        </div>
                        <div class="card-header py-3">
                            <a href="tambah_siswa.php" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i> Tambah Siswa</a>
                            <!-- Form Upload Excel -->
                            <form method="POST" action="" enctype="multipart/form-data" style="display:inline;">
                                <input type="file" name="excel_file" accept=".xlsx, .xls" required>
                                <button type="submit" name="import_excel" class="btn btn-primary btn-sm"><i class="fas fa-file-import"></i> Import Excel</button>
                            </form>
                            <!-- Tombol Unduh Format Excel -->
                            <a href="../assets/format_siswa.xlsx" class="btn btn-info btn-sm" download><i class="fas fa-download"></i> Unduh Format Excel</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Alamat</th>
                                        <th>Kelas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($siswa_list)): ?>
                                        <?php foreach ($siswa_list as $siswa): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($siswa['nama_siswa']); ?></td>
                                                <td><?php echo htmlspecialchars($siswa['nisn']); ?></td>
                                                <td><?php echo htmlspecialchars($siswa['jenis_kelamin']); ?></td>
                                                <td><?php echo htmlspecialchars($siswa['tanggal_lahir']); ?></td>
                                                <td><?php echo htmlspecialchars($siswa['alamat']); ?></td>
                                                <td><?php echo htmlspecialchars($siswa['nama_kelas']); ?></td>
                                                <td>
                                                    <a href="edit_siswa.php?id=<?php echo htmlspecialchars($siswa['id_siswa']); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    <a href="hapus_siswa.php?id=<?php echo htmlspecialchars($siswa['id_siswa']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data siswa.</td>
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