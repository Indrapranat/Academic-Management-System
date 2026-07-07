<?php
// Di student/index.php sudah: include '../Includes/dbcon.php'; include 'includes/session.php';
// Jadi di sini cukup pakai $conn dan $_SESSION yang sudah ada.

// Pastikan hanya siswa yang lewat sini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

$studentId = $_SESSION['studentId'] ?? null;

if (!$studentId) {
    // Kalau entah kenapa studentId kosong, paksa keluar
    header("Location: ../index.php");
    exit;
}

// ====== PERUBAHAN PENTING DI SINI ======
// Dulu: SELECT firstName, lastName FROM tblstudents ...
// Sekarang tabel sudah pakai kolom fullName
$query = "SELECT fullName FROM tblstudents WHERE Id = '$studentId' LIMIT 1";
$rs    = $conn->query($query);
$rows  = $rs ? $rs->fetch_assoc() : null;

$fullName = $rows ? $rows['fullName'] : "Student";
?>

<nav class="navbar navbar-expand navbar-light bg-gradient-primary topbar mb-4 static-top">
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <div class="text-white big" style="margin-left:100px;">
        <b>Student Panel</b>
    </div>

    <ul class="navbar-nav ml-auto">
        <!-- Search dropdown (opsional) -->
        <li class="nav-item dropdown no-arrow d-none d-sm-block">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw text-white"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                 aria-labelledby="searchDropdown">
                <form class="navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-1 small"
                               placeholder="Search..."
                               aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle" src="img/user-icn.png" style="max-width: 60px">
                <span class="ml-2 d-none d-lg-inline text-white small">
                    <b>Welcome <?php echo htmlspecialchars($fullName); ?></b>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../logout.php">
                    <i class="fas fa-power-off fa-fw mr-2 text-danger"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
