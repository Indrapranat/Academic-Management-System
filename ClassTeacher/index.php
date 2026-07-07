<?php
require_once __DIR__ . '/Includes/session.php';   // WAJIB paling atas (teacher guard)
require_once __DIR__ . '/../Includes/dbcon.php';  // koneksi DB

$teacherId  = (int)($_SESSION['userId'] ?? 0);
$classId    = (int)($_SESSION['classId'] ?? 0);
$classArmId = (int)($_SESSION['classArmId'] ?? 0);

// Default label
$rrw = [
    'className'    => 'N/A',
    'classArmName' => 'N/A',
];

if ($teacherId > 0) {
    // Ambil info class & arm yang dipegang teacher
    $query = "
        SELECT c.className, ca.classArmName, t.classId, t.classArmId
        FROM tblclassteacher t
        INNER JOIN tblclass c ON c.Id = t.classId
        INNER JOIN tblclassarms ca ON ca.Id = t.classArmId
        WHERE t.Id = $teacherId
        LIMIT 1
    ";
    $rs = $conn->query($query);
    if ($rs && $rs->num_rows === 1) {
        $rrw = $rs->fetch_assoc();

        // Sinkronkan session classId & classArmId dari DB (ini penting)
        $_SESSION['classId']    = (int)$rrw['classId'];
        $_SESSION['classArmId'] = (int)$rrw['classArmId'];

        $classId    = (int)$rrw['classId'];
        $classArmId = (int)$rrw['classArmId'];
    }
}

// Summary
$students = 0;
$totAttendance = 0;

if ($classId > 0 && $classArmId > 0) {
    $qStudents = mysqli_query(
        $conn,
        "SELECT Id FROM tblstudents WHERE classId = '$classId' AND classArmId = '$classArmId'"
    );
    $students = $qStudents ? mysqli_num_rows($qStudents) : 0;

    $qAttendance = mysqli_query(
        $conn,
        "SELECT Id FROM tblattendance WHERE classId = '$classId' AND classArmId = '$classArmId'"
    );
    $totAttendance = $qAttendance ? mysqli_num_rows($qAttendance) : 0;
}

$qClass = mysqli_query($conn, "SELECT Id FROM tblclass");
$class = $qClass ? mysqli_num_rows($qClass) : 0;

$qArms = mysqli_query($conn, "SELECT Id FROM tblclassarms");
$classArms = $qArms ? mysqli_num_rows($qArms) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Class Teacher Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

  <?php include __DIR__ . "/Includes/sidebar.php"; ?>

  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

      <?php include __DIR__ . "/Includes/topbar.php"; ?>

      <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">
            Class Teacher Dashboard (<?php echo htmlspecialchars($rrw['className'].' - '.$rrw['classArmName']); ?>)
          </h1>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
          </ol>
        </div>

        <div class="row mb-3">
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Students (Your Class)</div>
                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo (int)$students; ?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-users fa-2x text-info"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Classes</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo (int)$class; ?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-chalkboard fa-2x text-primary"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Class Arms</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo (int)$classArms; ?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-code-branch fa-2x text-success"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Student Attendance (Your Class)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo (int)$totAttendance; ?></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-calendar fa-2x text-warning"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>

    <?php include __DIR__ . '/Includes/footer.php'; ?>
  </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
  <i class="fas fa-angle-up"></i>
</a>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
<script src="../vendor/chart.js/Chart.min.js"></script>
<script src="js/demo/chart-area-demo.js"></script>
</body>
</html>
