<?php
// ====================================================
//  SAFE: Pengecekan koneksi + session
// ====================================================
if (!isset($conn)) {
    include '../../Includes/dbcon.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ====================================================
//  Ambil nama teacher
// ====================================================
$fullName = "Class Teacher";

if (!empty($_SESSION['userId'])) {

    $teacherId = (int) $_SESSION['userId'];

    $q = $conn->query("
        SELECT firstName, lastName 
        FROM tblclassteacher 
        WHERE Id = '$teacherId' 
        LIMIT 1
    ");

    if ($q && $q->num_rows === 1) {
        $t = $q->fetch_assoc();
        $fullName = htmlspecialchars($t['firstName'] . " " . $t['lastName']);
    }
}
?>

<!-- ====================================================
      TOPBAR UI
     ==================================================== -->

<nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow"
     style="background-color:#f5e6a3;"> <!-- kuning gading -->
    
    <!-- Toggle Sidebar -->
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Title -->
    <div class="text-dark font-weight-bold" style="margin-left:100px;">
        Class Teacher Panel
    </div>

    <ul class="navbar-nav ml-auto">

        <!-- Search -->
        <li class="nav-item dropdown no-arrow d-none d-sm-block">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown"
               role="button" data-toggle="dropdown">
                <i class="fas fa-search fa-fw text-dark"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                 aria-labelledby="searchDropdown">

                <form class="navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light small"
                               placeholder="Search...">
                        <div class="input-group-append">
                            <button class="btn btn-dark" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Menu -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown"
               role="button" data-toggle="dropdown">

                <img class="img-profile rounded-circle"
                     src="img/user-icn.png" style="max-width: 60px">

                <span class="ml-2 d-none d-lg-inline text-dark small">
                    <b>Welcome <?= $fullName; ?></b>
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="../logout.php">
                    <i class="fas fa-power-off fa-fw mr-2 text-danger"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
