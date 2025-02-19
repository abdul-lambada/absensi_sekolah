<?php
$title = "List Guru";
$active_page = "list_guru"; // Untuk menandai menu aktif di sidebar
include '../templates/header.php';
include '../templates/sidebar.php';

// Ambil data guru
include '../includes/db.php';
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
                            <a href="tambah_guru.php" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"></i></a>
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
                                    <?php foreach ($guru_list as $guru): ?>
                                        <tr>
                                            <td><?php echo $guru['nama_guru']; ?></td>
                                            <td><?php echo $guru['nip']; ?></td>
                                            <td><?php echo $guru['jenis_kelamin']; ?></td>
                                            <td><?php echo $guru['tanggal_lahir']; ?></td>
                                            <td><?php echo $guru['alamat']; ?></td>
                                            <td>
                                                <a href="edit_guru.php?id=<?php echo $guru['id_guru']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                <a href="hapus_guru.php?id=<?php echo $guru['id_guru']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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