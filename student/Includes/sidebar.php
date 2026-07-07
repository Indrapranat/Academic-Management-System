<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center bg-gradient-primary justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
            <img src="img/logo/attnlg.jpg" style="height:35px;">
        </div>
        <div class="sidebar-brand-text mx-3">Student AMS</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Kehadiran Saya
    </div>

    <!-- Riwayat Absensi -->
    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'attendance-history.php' ? 'active' : ''; ?>">
        <a class="nav-link" href="attendance-history.php">
            <i class="fas fa-calendar-check"></i>
            <span>Riwayat Absensi</span>
        </a>
    </li>

    <!-- Rekap Hadir / Alfa (masih pakai halaman yang sama, beda anchor) -->
    <li class="nav-item">
        <a class="nav-link" href="attendance-history.php#summary">
            <i class="fas fa-chart-pie"></i>
            <span>Rekap Hadir / Alfa</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Kelas & Dosen
    </div>

    <!-- Info Dosen / Wali Kelas (guru wali sesuai class & arm student) -->
    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'my-teacher.php' ? 'active' : ''; ?>">
        <a class="nav-link" href="my-teacher.php">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Dosen / Wali Kelas</span>
        </a>
    </li>

    <!-- Daftar semua dosen / wali kelas -->
    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'teachers.php' ? 'active' : ''; ?>">
        <a class="nav-link" href="teachers.php">
            <i class="fas fa-users"></i>
            <span>Daftar Dosen</span>
        </a>
    </li>

    <!-- Shortcut ke Kelas yang Diikuti -->
    <li class="nav-item">
        <a class="nav-link" href="index.php#kelas">
            <i class="fas fa-users-class"></i>
            <span>Kelas yang Diikuti</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="version" id="version-ruangadmin"></div>
</ul>
