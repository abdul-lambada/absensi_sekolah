<?php
$title = "List Siswa";
$active_page = "list_siswa"; // Untuk menandai menu aktif di sidebar
include '../templates/header.php';
include '../templates/sidebar.php';

// Koneksi ke database
include '../includes/db.php';

// Ambil data siswa beserta nama kelas
$stmt = $conn->query("SELECT s.*, k.nama_kelas FROM Siswa s JOIN Kelas k ON s.id_kelas = k.id_kelas");
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
                            <a href="tambah_siswa.php" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i></a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
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
                                                <td><?php echo $siswa['nisn']; ?></td>
                                                <td><?php echo $siswa['nama_siswa']; ?></td>
                                                <td><?php echo $siswa['jenis_kelamin']; ?></td>
                                                <td><?php echo $siswa['tanggal_lahir']; ?></td>
                                                <td><?php echo $siswa['alamat']; ?></td>
                                                <td><?php echo $siswa['nama_kelas']; ?></td>
                                                <td>
                                                    <a href="edit_siswa.php?id=<?php echo $siswa['id_siswa']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    <a href="hapus_siswa.php?id=<?php echo $siswa['id_siswa']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></a>
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