<?php
include '../Includes/dbcon.php';
include 'includes/session.php';

// Ambil semua guru + info kelas
$teachers = mysqli_query(
    $conn,
    "SELECT t.firstName,
            t.lastName,
            t.emailAddress,
            t.phoneNo,
            c.className,
            ca.classArmName
     FROM tblclassteacher t
     INNER JOIN tblclass c ON c.Id = t.classId
     INNER JOIN tblclassarms ca ON ca.Id = t.classArmId
     ORDER BY c.className, ca.classArmName, t.lastName"
);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Daftar Dosen / Wali Kelas</title>

    <!-- SESUAIKAN PATH BERIKUT DENGAN FILE YANG SUDAH ADA DI PROJECT -->
    <!-- Biasanya struktur RuangAdmin seperti ini: -->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../css/ruang-admin.min.css" rel="stylesheet">

</head>

<body id="page-top">

<div id="wrapper">

    <!-- Sidebar siswa -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Topbar sederhana -->
            <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
                <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <span class="navbar-brand mb-0 h5 text-white">Student Panel</span>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid" id="container-wrapper">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Daftar Dosen / Wali Kelas</h1>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Semua Dosen / Wali Kelas</h6>
                            </div>
                            <div class="table-responsive p-3">
                                <table class="table align-items-center table-flush table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Telepon</th>
                                            <th>Kelas</th>
                                            <th>Class Arm</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        if (mysqli_num_rows($teachers) > 0):
                                            while ($row = mysqli_fetch_assoc($teachers)):
                                        ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($row['firstName'].' '.$row['lastName']); ?></td>
                                                <td><?php echo htmlspecialchars($row['emailAddress']); ?></td>
                                                <td><?php echo htmlspecialchars($row['phoneNo']); ?></td>
                                                <td><?php echo htmlspecialchars($row['className']); ?></td>
                                                <td><?php echo htmlspecialchars($row['classArmName']); ?></td>
                                            </tr>
                                        <?php
                                            endwhile;
                                        else:
                                        ?>
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    Belum ada data dosen / wali kelas.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="row">
                    <div class="col-lg-12 text-center mb-4">
                        <small>© 2025 - Developed by Ryan Manajemen Kehadiran Siswa</small>
                    </div>
                </div>

            </div><!-- /.container-fluid -->

        </div><!-- End of content -->
    </div><!-- End of content-wrapper -->

</div><!-- End of wrapper -->

<!-- JS (sesuaikan path dengan project-mu) -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/ruang-admin.min.js"></script>

</body>
</html>
