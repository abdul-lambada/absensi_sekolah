<?php
$title = "List Kelas";
$active_page = "list_kelas"; // Untuk menandai menu aktif di sidebar
include '../templates/header.php';
include '../templates/sidebar.php';

// Koneksi ke database
include '../includes/db.php';

// Set pagination variables
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Query total rows from Kelas table
$countStmt = $conn->query("SELECT COUNT(*) as total FROM Kelas");
$totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRows / $limit);

// Ambil data Kelas beserta nama jurusan dengan limit dan offset
$stmt = $conn->prepare("SELECT k.*, j.nama_jurusan FROM Kelas k JOIN Jurusan j ON k.id_jurusan = j.id_jurusan LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cek status dari query string
$status = isset($_GET['status']) ? $_GET['status'] : '';
$message = '';

switch ($status) {
    case 'add_success':
        $message = 'Data kelas berhasil ditambahkan.';
        $alert_class = 'alert-success';
        break;
    case 'edit_success':
        $message = 'Data kelas berhasil diperbarui.';
        $alert_class = 'alert-warning';
        break;
    case 'delete_success':
        $message = 'Data kelas berhasil dihapus.';
        $alert_class = 'alert-danger';
        break;
    case 'error':
        $message = 'Terjadi kesalahan saat memproses data.';
        $alert_class = 'alert-danger';
        break;
    default:
        $message = '';
        $alert_class = '';
        break;
}
?>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">List Kelas</h1>
        </nav>
        <div class="container-fluid">
            <!-- Begin Alert SB Admin 2 -->
            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $alert_class; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <!-- End Alert SB Admin 2 -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Kelas</h6>
                        </div>
                        <div class="card-header py-3">
                            <a href="tambah_kelas.php" class="btn btn-success btn-sm"><i class="fas fa-plus-circle"> Tambah Data</i></a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Kelas</th>
                                        <th>Jurusan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($kelas_list)): ?>
                                        <?php foreach ($kelas_list as $kelas): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($kelas['nama_kelas']); ?></td>
                                                <td><?php echo htmlspecialchars($kelas['nama_jurusan']); ?></td>
                                                <td>
                                                    <a href="edit_kelas.php?id=<?php echo $kelas['id_kelas']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"> Edit</i></a>
                                                    <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#logoutModal"><i class="fas fa-trash"> Hapus</i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data kelas.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <!-- Dynamic Pagination SB Admin 2 -->
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-end">
                                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>" tabindex="-1">Previous</a>
                                    </li>
                                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hapus Data</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Apakah Kamu Yakin, Akan Menghapus Data Ini.!</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="hapus_kelas.php?id=<?php echo $kelas['id_kelas']; ?>">Hapus</a>
            </div>
        </div>
    </div>
</div>
<?php include '../templates/footer.php'; ?>