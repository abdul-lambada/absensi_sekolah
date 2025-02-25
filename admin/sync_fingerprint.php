<?php
$title = "Sinkronisasi Fingerprint";
$active_page = "sync_fingerprint"; // Untuk menandai menu aktif di sidebar
include '../templates/header.php';
include '../templates/sidebar.php';
// Include koneksi database
include '../includes/db.php';
require_once '../includes/zklib/zklibrary.php'; // Library ZKTeco

// Variabel untuk menyimpan pesan alert
$connection_message = '';
$connection_class = '';
$synchronized_data = array(); // Menyimpan data absensi yang berhasil disinkronkan
$device_info = array(); // Menyimpan informasi perangkat (nama, versi firmware, serial number)

// Proses sinkronisasi jika tombol ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sync'])) {
    $device_ip = trim($_POST['device_ip']); // Ambil IP dari form

    // Validasi input IP
    if (empty($device_ip)) {
        $connection_message = 'IP Address tidak boleh kosong.';
        $connection_class = 'alert-danger';
    } else {
        try {
            // Inisialisasi library ZKLibrary
            $zk = new ZKLibrary($device_ip);

            // Coba hubungkan ke perangkat fingerprint
            if ($zk->connect()) {
                $connection_message = 'Berhasil terhubung ke perangkat fingerprint dengan IP: ' . htmlspecialchars($device_ip, ENT_QUOTES);
                $connection_class = 'alert-success';

                // Nonaktifkan perangkat sementara
                $zk->disableDevice();

                // Ambil informasi perangkat
                $device_info['name'] = $zk->getDeviceName();
                $device_info['firmware'] = $zk->getFirmwareVersion();
                $device_info['serial'] = $zk->getSerialNumber();

                // Ambil data absensi
                $attendance = $zk->getAttendance();

                // Aktifkan kembali perangkat
                $zk->enableDevice();
                $zk->disconnect();

                // Simpan data absensi ke variabel untuk ditampilkan
                foreach ($attendance as $record) {
                    $synchronized_data[] = array(
                        'id_siswa' => $record['uid'], // ID siswa dari fingerprint
                        'tanggal' => date('Y-m-d', strtotime($record['timestamp'])),
                        'jam_masuk' => date('H:i:s', strtotime($record['timestamp']))
                    );
                }

                if (empty($synchronized_data)) {
                    $connection_message .= ' Tidak ada data absensi baru.';
                }
            } else {
                // Jika gagal terhubung
                $connection_message = 'Gagal terhubung ke perangkat fingerprint. Periksa IP Address: ' . htmlspecialchars($device_ip, ENT_QUOTES);
                $connection_class = 'alert-danger';
            }
        } catch (Exception $e) {
            // Tangkap error jika terjadi masalah saat menghubungkan
            $connection_message = 'Terjadi kesalahan: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES);
            $connection_class = 'alert-danger';
        }
    }
}
?>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Sinkronisasi Fingerprint</h1>
        </nav>
        <div class="container-fluid">
            <!-- Begin Alert SB Admin 2 -->
            <?php if (!empty($connection_message)): ?>
                <div class="alert <?php echo $connection_class; ?> alert-dismissible fade show" role="alert">
                    <?php echo $connection_message; ?>
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
                            <h6 class="m-0 font-weight-bold text-primary">Sinkronisasi Data Absensi</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" id="syncForm">
                                <div class="mb-3">
                                    <label for="device_ip" class="form-label">IP Address Perangkat Fingerprint</label>
                                    <input type="text" class="form-control" id="device_ip" name="device_ip" placeholder="Contoh: 192.168.1.201" required>
                                </div>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">
                                    <i class="fas fa-sync-alt"></i> Sinkronkan Data Absensi
                                </button>
                            </form>

                            <!-- Tampilkan informasi perangkat -->
                            <?php if (!empty($device_info)): ?>
                                <hr>
                                <h5>Informasi Perangkat:</h5>
                                <ul>
                                    <li><strong>Nama Perangkat:</strong> <?php echo htmlspecialchars($device_info['name'], ENT_QUOTES); ?></li>
                                    <li><strong>Versi Firmware:</strong> <?php echo htmlspecialchars($device_info['firmware'], ENT_QUOTES); ?></li>
                                    <li><strong>Nomor Seri:</strong> <?php echo htmlspecialchars($device_info['serial'], ENT_QUOTES); ?></li>
                                </ul>
                            <?php endif; ?>

                            <!-- Tampilkan data absensi yang berhasil disinkronkan -->
                            <?php if (!empty($synchronized_data)): ?>
                                <hr>
                                <h5>Data Absensi yang Disinkronkan:</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID Siswa</th>
                                            <th>Tanggal</th>
                                            <th>Jam Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($synchronized_data as $data): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($data['id_siswa'], ENT_QUOTES); ?></td>
                                                <td><?php echo htmlspecialchars($data['tanggal'], ENT_QUOTES); ?></td>
                                                <td><?php echo htmlspecialchars($data['jam_masuk'], ENT_QUOTES); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <!-- Tombol untuk menghapus log absensi dari perangkat -->
                                <form method="POST" action="">
                                    <input type="hidden" name="device_ip" value="<?php echo htmlspecialchars($device_ip, ENT_QUOTES); ?>">
                                    <button type="submit" name="clear_attendance" class="btn btn-danger mt-3">
                                        <i class="fas fa-trash"></i> Hapus Log Absensi dari Perangkat
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Sinkronisasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Sinkronisasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin melakukan sinkronisasi data absensi dari perangkat fingerprint?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" form="syncForm" class="btn btn-primary">Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<?php
// Proses untuk menghapus log absensi dari perangkat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_attendance'])) {
    $device_ip = trim($_POST['device_ip']);

    try {
        $zk = new ZKLibrary($device_ip);
        if ($zk->connect()) {
            $zk->clearAttendance(); // Hapus semua log absensi dari perangkat
            $zk->disconnect();
            $connection_message = 'Log absensi berhasil dihapus dari perangkat.';
            $connection_class = 'alert-success';
        } else {
            $connection_message = 'Gagal terhubung ke perangkat fingerprint. Periksa IP Address: ' . htmlspecialchars($device_ip, ENT_QUOTES);
            $connection_class = 'alert-danger';
        }
    } catch (Exception $e) {
        $connection_message = 'Terjadi kesalahan: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES);
        $connection_class = 'alert-danger';
    }
}
?>

<?php include '../templates/footer.php'; ?>