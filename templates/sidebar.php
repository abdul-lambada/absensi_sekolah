<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i> <!-- Updated icon per SB Admin2 -->
        </div>
        <div class="sidebar-brand-text mx-3">Absensi Sekolah</div>
    </a>
    <hr class="sidebar-divider my-0">
    <!-- Dashboard -->
    <li class="nav-item <?php echo $active_page === 'dashboard' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <hr class="sidebar-divider">
    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <div class="sidebar-heading">
            Data Master
        </div>
        <li class="nav-item <?php echo $active_page === 'sync_fingerprint' ? 'active' : ''; ?>">
            <a class="nav-link" href="sync_fingerprint.php">
                <i class="fas fa-fw fa-sync-alt"></i> <!-- Icon untuk sinkronisasi -->
                <span>Sinkronisasi Fingerprint</span>
            </a>
        </li>
        <li class="nav-item <?php echo $active_page === 'list_jurusan' ? 'active' : ''; ?>">
            <a class="nav-link" href="list_jurusan.php">
                <i class="fas fa-fw fa-building"></i> <!-- Changed icon for jurusan -->
                <span>Data Jurusan</span>
            </a>
        </li>
        <li class="nav-item <?php echo $active_page === 'list_kelas' ? 'active' : ''; ?>">
            <a class="nav-link" href="list_kelas.php">
                <i class="fas fa-fw fa-door-open"></i> <!-- Changed icon for kelas -->
                <span>Data Kelas</span>
            </a>
        </li>
        <li class="nav-item <?php echo $active_page === 'list_guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="list_guru.php">
                <i class="fas fa-fw fa-chalkboard-teacher"></i> <!-- Changed icon for guru -->
                <span>List Guru</span>
            </a>
        </li>
        <li class="nav-item <?php echo $active_page === 'list_siswa' ? 'active' : ''; ?>">
            <a class="nav-link" href="list_siswa.php">
                <i class="fas fa-fw fa-user-graduate"></i>
                <span>List Siswa</span>
            </a>
        </li>
        <!-- Link untuk tabel users -->
        <li class="nav-item <?php echo $active_page === 'list_users' ? 'active' : ''; ?>">
            <a class="nav-link" href="list_users.php">
                <i class="fas fa-fw fa-users"></i> <!-- Icon untuk users -->
                <span>Data Pengguna</span>
            </a>
        </li>
        <!-- Link untuk tabel attendance_records -->
        <li class="nav-item <?php echo $active_page === 'attendance_records' ? 'active' : ''; ?>">
            <a class="nav-link" href="attendance_records.php">
                <i class="fas fa-fw fa-clock"></i> <!-- Icon untuk absensi -->
                <span>Log Absensi</span>
            </a>
        </li>
        <li class="nav-item <?php echo $active_page === 'laporan_guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="laporan_guru.php">
                <i class="fas fa-fw fa-user-check"></i> <!-- Changed icon for laporan absensi guru -->
                <span>Laporan Absensi Guru</span>
            </a>
        </li>
        <li class="nav-item <?php echo $active_page === 'laporan_siswa' ? 'active' : ''; ?>">
            <a class="nav-link" href="laporan_siswa.php">
                <i class="fas fa-fw fa-file-alt"></i> <!-- Changed icon for laporan absensi siswa -->
                <span>Laporan Absensi Siswa</span>
            </a>
        </li>
        <!-- Menu Layanan Pengaduan -->
        <li class="nav-item <?php echo $active_page === 'list_pengaduan' ? 'active' : ''; ?>">
            <a class="nav-link" href="list_pengaduan.php">
                <i class="fas fa-fw fa-exclamation-circle"></i> <!-- Icon for layanan pengaduan -->
                <span>Layanan Pengaduan</span>
            </a>
        </li>
        <hr class="sidebar-divider">
    <?php endif; ?>
    <?php if ($_SESSION['user']['role'] === 'guru'): ?>
        <div class="sidebar-heading">
            Guru Menu
        </div>
        <li class="nav-item <?php echo $active_page === 'absensi_siswa' ? 'active' : ''; ?>">
            <a class="nav-link" href="absensi_siswa.php">
                <i class="fas fa-fw fa-clipboard-list"></i>
                <span>Absensi Siswa</span>
            </a>
        </li>
        <li class="nav-item <?php echo $active_page === 'absensi_guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="absensi_guru.php">
                <i class="fas fa-fw fa-user-clock"></i> <!-- Changed icon for absensi guru -->
                <span>Absensi Guru</span>
            </a>
        </li>
        <li class="nav-item <?php echo $active_page === 'laporan_siswa' ? 'active' : ''; ?>">
            <a class="nav-link" href="laporan.php">
                <i class="fas fa-fw fa-file-alt"></i> <!-- Changed icon for laporan absensi siswa -->
                <span>Laporan Absensi Siswa</span>
            </a>
        </li>
        <li class="nav-item <?php echo $active_page === 'laporan_guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="laporan_guru.php">
                <i class="fas fa-fw fa-user-check"></i>
                <span>Laporan Absensi Guru</span>
            </a>
        </li>
        <hr class="sidebar-divider">
    <?php endif; ?>
    <!-- Logout -->
    <li class="nav-item">
        <a class="nav-link" href="../auth/logout.php">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle" type="button"></button>
    </div>
</ul>
<!-- End of Sidebar -->