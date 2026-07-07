<?php
include '../Includes/dbcon.php';
include 'includes/session.php';

$classId    = $_SESSION['classId'];
$classArmId = $_SESSION['classArmId'];

// Ambil teacher yg handle class + arm ini
$q = "
    SELECT firstName, lastName, emailAddress, phoneNo
    FROM tblclassteacher
    WHERE classId = '$classId' AND classArmId = '$classArmId'
    LIMIT 1
";
$res = mysqli_query($conn, $q);
$teacher = $res && mysqli_num_rows($res) ? mysqli_fetch_assoc($res) : null;

// Ambil info kelas
$cRes = mysqli_query($conn, "
    SELECT c.className, a.classArmName
    FROM tblclass c
    JOIN tblclassarms a ON a.classId = c.Id AND a.Id = '$classArmId'
    WHERE c.Id = '$classId'
    LIMIT 1
");
$classInfo = $cRes && mysqli_num_rows($cRes) ? mysqli_fetch_assoc($cRes) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dosen / Wali Kelas</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">
  <?php include 'includes/sidebar.php'; ?>
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
      <?php include 'includes/topbar.php'; ?>

      <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Dosen / Wali Kelas</h1>
        </div>

        <div class="row mb-3">
          <div class="col-lg-6">
            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Kelas</h6>
              </div>
              <div class="card-body">
                <?php if ($classInfo): ?>
                    <p>Class: <strong><?php echo htmlspecialchars($classInfo['className']); ?></strong></p>
                    <p>Class Arm: <strong><?php echo htmlspecialchars($classInfo['classArmName']); ?></strong></p>
                <?php else: ?>
                    <p>Data kelas tidak tersedia.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dosen / Class Teacher</h6>
              </div>
              <div class="card-body">
                <?php if ($teacher): ?>
                    <p>Nama: <strong><?php echo htmlspecialchars($teacher['firstName'].' '.$teacher['lastName']); ?></strong></p>
                    <p>Email: <strong><?php echo htmlspecialchars($teacher['emailAddress']); ?></strong></p>
                    <p>Telepon: <strong><?php echo htmlspecialchars($teacher['phoneNo']); ?></strong></p>
                <?php else: ?>
                    <p>Belum ada dosen / class teacher yang ter-assign untuk kelas ini.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Catatan jadwal/absen -->
        <div class="row mb-3">
          <div class="col-lg-12">
            <div class="alert alert-secondary">
              Absensi dilakukan per hari untuk kelas ini. Gunakan tombol
              <strong>"Absen Hadir Sekarang"</strong> di Dashboard pada hari aktif belajar.
            </div>
          </div>
        </div>

      </div>
    </div>
    <?php include 'includes/footer.php'; ?>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
</body>
</html>
