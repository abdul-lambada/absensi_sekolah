<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-school"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Absensi Sekolah</div>
    </a>
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item <?php echo $active_page === 'dashboard' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Menu untuk Admin -->
    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <li class="nav-item <?php echo $active_page === 'list_jurusan' ? 'active' : ''; ?>">
            <a class="nav-link" href="list_jurusan.php">
                <i class="fas fa-fw fa-users"></i>
                <span>Data Jurusan</span></a>
        </li>
        <li class="nav-item <?php echo $active_page === 'list_kelas' ? 'active' : ''; ?>">
            <a class="nav-link" href="list_kelas.php">
                <i class="fas fa-fw fa-users"></i>
                <span>Data Kelas</span></a>
        </li>
        <li class="nav-item <?php echo $active_page === 'list_guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="list_guru.php">
                <i class="fas fa-fw fa-users"></i>
                <span>List Guru</span></a>
        </li>
        <li class="nav-item <?php echo $active_page === 'list_siswa' ? 'active' : ''; ?>">
            <a class="nav-link" href="list_siswa.php">
                <i class="fas fa-fw fa-user-graduate"></i>
                <span>List Siswa</span></a>
        </li>
        <li class="nav-item <?php echo $active_page === 'laporan_guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="laporan_guru.php">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan Absensi Guru</span></a>
        </li>
        <li class="nav-item <?php echo $active_page === 'laporan_siswa' ? 'active' : ''; ?>">
            <a class="nav-link" href="laporan_siswa.php">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan Absensi Siswa</span></a>
        </li>
    <?php endif; ?>

    <!-- Menu untuk Guru -->
    <?php if ($_SESSION['user']['role'] === 'guru'): ?>
        <li class="nav-item <?php echo $active_page === 'absensi_siswa' ? 'active' : ''; ?>">
            <a class="nav-link" href="absensi_siswa.php">
                <i class="fas fa-fw fa-clipboard-list"></i>
                <span>Absensi Siswa</span></a>
        </li>
        <li class="nav-item <?php echo $active_page === 'absensi_guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="absensi_guru.php">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Absensi Guru</span></a>
        </li>
        <li class="nav-item <?php echo $active_page === 'laporan_siswa' ? 'active' : ''; ?>">
            <a class="nav-link" href="laporan.php">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan Absensi Siswa</span></a>
        </li>
        <li class="nav-item <?php echo $active_page === 'laporan_guru' ? 'active' : ''; ?>">
            <a class="nav-link" href="laporan_guru.php">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan Absensi Guru</span></a>
        </li>
    <?php endif; ?>
    <!-- Menu Logout -->
    <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item">
        <a class="nav-link" href="../auth/logout.php">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span></a>
    </li>
</ul>
<!-- End of Sidebar -->