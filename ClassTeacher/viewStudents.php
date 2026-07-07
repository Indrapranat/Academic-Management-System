<?php
require_once __DIR__ . '/Includes/session.php';   // ✅ teacher guard (bootRoleSession teacher)
require_once __DIR__ . '/../Includes/dbcon.php';  // ✅ DB

// --------------------------------------------------
// Info kelas yang dipegang teacher
// --------------------------------------------------
$teacherId  = (int)($_SESSION['userId'] ?? 0);
$classId    = (int)($_SESSION['classId'] ?? 0);
$classArmId = (int)($_SESSION['classArmId'] ?? 0);

if ($teacherId <= 0) {
    header("Location: ../index.php");
    exit;
}

$classInfo = [
    'className'    => 'Unknown',
    'classArmName' => 'Unknown',
];

$qClass = mysqli_query($conn, "
    SELECT 
        c.Id  AS classId,
        c.className,
        a.Id  AS classArmId,
        a.classArmName
    FROM tblclassteacher t
    JOIN tblclass     c ON c.Id = t.classId
    JOIN tblclassarms a ON a.Id = t.classArmId
    WHERE t.Id = '$teacherId'
    LIMIT 1
");

if ($qClass && mysqli_num_rows($qClass) === 1) {
    $row = mysqli_fetch_assoc($qClass);

    $classInfo['className']    = $row['className'];
    $classInfo['classArmName'] = $row['classArmName'];

    if ($classId === 0) {
        $_SESSION['classId'] = $classId = (int)$row['classId'];
    }
    if ($classArmId === 0) {
        $_SESSION['classArmId'] = $classArmId = (int)$row['classArmId'];
    }
}

// --------------------------------------------------
// Ambil semua siswa di kelas ini
// --------------------------------------------------
$studentsRes = null;
if ($classId > 0 && $classArmId > 0) {
    $studentsRes = mysqli_query($conn, "
        SELECT 
            s.Id,
            s.fullName,
            s.admissionNumber,
            c.className,
            a.classArmName
        FROM tblstudents s
        JOIN tblclass     c ON c.Id = s.classId
        JOIN tblclassarms a ON a.Id = s.classArmId
        WHERE s.classId    = '$classId'
          AND s.classArmId = '$classArmId'
        ORDER BY s.fullName ASC
    ");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>All Students In Class</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">

    <?php include "Includes/sidebar.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">

        <?php include "Includes/topbar.php"; ?>

        <div class="container-fluid" id="container-wrapper">

          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
              Siswa di Kelas (<?= htmlspecialchars($classInfo['className'] . ' - ' . $classInfo['classArmName']); ?>)
            </h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Students</li>
            </ol>
          </div>

          <div class="mb-3">
            <a href="addStudent.php" class="btn btn-primary btn-sm">
              <i class="fas fa-user-plus"></i> Tambah Siswa
            </a>
          </div>

          <div class="card mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive p-3">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                  <thead class="thead-light">
                    <tr>
                      <th>#</th>
                      <th>Nama</th>
                      <th>NIM</th>
                      <th>Class</th>
                      <th>Class Arm</th>
                      <th style="width:130px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i = 1;
                    if ($studentsRes && mysqli_num_rows($studentsRes) > 0):
                        while ($row = mysqli_fetch_assoc($studentsRes)):
                    ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= htmlspecialchars($row['fullName']); ?></td>
                      <td><?= htmlspecialchars($row['admissionNumber']); ?></td>
                      <td><?= htmlspecialchars($row['className']); ?></td>
                      <td><?= htmlspecialchars($row['classArmName']); ?></td>
                      <td>
                        <a href="editStudent.php?id=<?= (int)$row['Id']; ?>" 
                           class="btn btn-sm btn-info">
                           <i class="fas fa-edit"></i>
                        </a>
                        <a href="deleteStudent.php?id=<?= (int)$row['Id']; ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Hapus siswa ini? Semua data absensi siswa juga akan dihapus.');">
                           <i class="fas fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <tr>
                      <td colspan="6" class="text-center">Belum ada siswa pada kelas ini.</td>
                    </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div><!-- /.container-fluid -->

      </div>

      <?php include "Includes/footer.php"; ?>

    </div>
  </div>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable();
    });
  </script>
</body>
</html>
