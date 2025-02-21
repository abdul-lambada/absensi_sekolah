<?php
$title = "Dashboard";
$active_page = "dashboard"; // Untuk menandai menu aktif di sidebar
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/sidebar.php';

// Koneksi database
include __DIR__ . '/../includes/db.php';

// Mengambil statistik dari database
try {
    // Jumlah Siswa
    $stmt_siswa = $conn->query("SELECT COUNT(*) AS total_siswa FROM Siswa");
    $total_siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC)['total_siswa'];

    // Jumlah Guru
    $stmt_guru = $conn->query("SELECT COUNT(*) AS total_guru FROM Guru");
    $total_guru = $stmt_guru->fetch(PDO::FETCH_ASSOC)['total_guru'];

    // Jumlah Kelas
    $stmt_kelas = $conn->query("SELECT COUNT(*) AS total_kelas FROM Kelas");
    $total_kelas = $stmt_kelas->fetch(PDO::FETCH_ASSOC)['total_kelas'];

    // Jumlah Absensi Guru
    $stmt_absensi_guru = $conn->query("SELECT COUNT(*) AS total_absensi_guru FROM Absensi_guru");
    $total_absensi_guru = $stmt_absensi_guru->fetch(PDO::FETCH_ASSOC)['total_absensi_guru'];

    // Jumlah Absensi Siswa
    $stmt_absensi_siswa = $conn->query("SELECT COUNT(*) AS total_absensi_siswa FROM Absensi_siswa");
    $total_absensi_siswa = $stmt_absensi_siswa->fetch(PDO::FETCH_ASSOC)['total_absensi_siswa'];
} catch (\PDOException $e) {
    echo "<script>alert('Error saat mengambil data statistik: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
?>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">Selamat Datang, Admin</h1>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <!-- Statistik Jumlah Siswa -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Siswa
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_siswa; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik Jumlah Guru -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Guru
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_guru; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik Jumlah Kelas -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Kelas
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_kelas; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-school fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik Jumlah Absensi Guru -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total Absensi Guru
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_absensi_guru; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik Jumlah Absensi Siswa -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Total Absensi Siswa
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($total_absensi_siswa); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Grafik Jumlah Siswa per Kelas -->
                <div class="col-lg-12 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Jumlah Siswa per Kelas</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="siswaPerKelasChart" width="100%" height="50"></canvas>
                        </div>
                    </div>
                </div>

                <script>
                    // Data untuk grafik jumlah siswa per kelas
                    document.addEventListener("DOMContentLoaded", function() {
                        const ctx = document.getElementById('siswaPerKelasChart').getContext('2d');
                        const siswaPerKelasChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: [
                                    <?php
                                    try {
                                        $stmt_kelas = $conn->query("SELECT nama_kelas FROM Kelas");
                                        $kelas_list = $stmt_kelas->fetchAll(PDO::FETCH_COLUMN);
                                        echo "'" . implode("','", $kelas_list) . "'";
                                    } catch (\PDOException $e) {
                                        echo "[]";
                                    }
                                    ?>
                                ],
                                datasets: [{
                                    label: 'Jumlah Siswa',
                                    data: [
                                        <?php
                                        try {
                                            $stmt_siswa_per_kelas = $conn->query("
                                SELECT COUNT(*) AS jumlah_siswa 
                                FROM Siswa 
                                GROUP BY id_kelas
                            ");
                                            $jumlah_siswa = $stmt_siswa_per_kelas->fetchAll(PDO::FETCH_COLUMN);
                                            echo implode(",", $jumlah_siswa);
                                        } catch (\PDOException $e) {
                                            echo "[]";
                                        }
                                        ?>
                                    ],
                                    backgroundColor: [
                                        'rgba(75, 192, 192, 0.6)',
                                        'rgba(255, 99, 132, 0.6)',
                                        'rgba(54, 162, 235, 0.6)',
                                        'rgba(255, 206, 86, 0.6)'
                                    ],
                                    borderColor: [
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    });
                </script>

                <!-- Peringkat Kelas Berdasarkan Absensi -->
                <div class="col-lg-12 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Peringkat Kelas Berdasarkan Kehadiran</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Kelas</th>
                                        <th>Total Siswa</th>
                                        <th>Total Hadir</th>
                                        <th>Persentase Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        // Cek apakah kolom 'status_kehadiran' ada di tabel Absensi_siswa
                                        $stmt_check_column = $conn->query("
                            SELECT COUNT(*) AS column_exists
                            FROM INFORMATION_SCHEMA.COLUMNS
                            WHERE TABLE_NAME = 'Absensi_siswa' AND COLUMN_NAME = 'status_kehadiran'
                        ");
                                        $column_exists = $stmt_check_column->fetch(PDO::FETCH_ASSOC)['column_exists'];

                                        if ($column_exists) {
                                            $stmt_peringkat_kelas = $conn->query("
                                SELECT k.nama_kelas, COUNT(s.id_siswa) AS total_siswa, 
                                       SUM(CASE WHEN a.status_kehadiran = 'Hadir' THEN 1 ELSE 0 END) AS total_hadir
                                FROM Kelas k
                                LEFT JOIN Siswa s ON k.id_kelas = s.id_kelas
                                LEFT JOIN Absensi_siswa a ON s.id_siswa = a.id_siswa
                                GROUP BY k.id_kelas
                                ORDER BY total_hadir DESC
                            ");
                                            $peringkat_kelas = $stmt_peringkat_kelas->fetchAll(PDO::FETCH_ASSOC);

                                            if (!empty($peringkat_kelas)) {
                                                foreach ($peringkat_kelas as $kelas) {
                                                    $persentase = ($kelas['total_siswa'] > 0) ? round(($kelas['total_hadir'] / $kelas['total_siswa']) * 100, 2) : 0;
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($kelas['nama_kelas']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($kelas['total_siswa']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($kelas['total_hadir']) . "</td>";
                                                    echo "<td>" . $persentase . "%</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='4' class='text-center'>Tidak ada data absensi.</td></tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center'>Kolom 'status_kehadiran' tidak ditemukan di tabel Absensi_siswa.</td></tr>";
                                        }
                                    } catch (\PDOException $e) {
                                        echo "<tr><td colspan='4' class='text-center'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>