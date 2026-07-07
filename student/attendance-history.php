<?php
include '../Includes/dbcon.php';
include 'includes/session.php';

$admissionNo = $_SESSION['admissionNumber'];
$classId     = $_SESSION['classId'];
$classArmId  = $_SESSION['classArmId'];

// Summary hadir / alfa
$statRes = mysqli_query(
    $conn,
    "SELECT 
        SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) AS totalPresent,
        SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) AS totalAbsent
     FROM tblattendance
     WHERE admissionNo = '$admissionNo'
       AND classId = '$classId'
       AND classArmId = '$classArmId'"
);
$stat = $statRes ? mysqli_fetch_assoc($statRes) : ['totalPresent' => 0, 'totalAbsent' => 0];

// Detail riwayat
$q = "
    SELECT a.dateTimeTaken, a.status,
           c.className, ca.classArmName,
           st.sessionName, t.termName
    FROM tblattendance a
    JOIN tblclass c ON c.Id = a.classId
    JOIN tblclassarms ca ON ca.Id = a.classArmId
    JOIN tblsessionterm st ON st.Id = a.sessionTermId
    JOIN tblterm t ON t.Id = st.termId
    WHERE a.admissionNo = '$admissionNo'
      AND a.classId = '$classId'
      AND a.classArmId = '$classArmId'
    ORDER BY a.dateTimeTaken DESC
";
$res = mysqli_query($conn, $q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Riwayat Absensi</title>
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
          <h1 class="h3 mb-0 text-gray-800">Riwayat Absensi</h1>
        </div>

        <!-- Summary -->
        <div class="row mb-3" id="summary">
          <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success h-100">
              <div class="card-body">
                <h6 class="font-weight-bold text-success">Total Hadir</h6>
                <h3><?php echo (int)$stat['totalPresent']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger h-100">
              <div class="card-body">
                <h6 class="font-weight-bold text-danger">Total Alfa</h6>
                <h3><?php echo (int)$stat['totalAbsent']; ?></h3>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabel Riwayat -->
        <div class="row mb-3">
          <div class="col-lg-12">
            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detail Kehadiran</h6>
              </div>
              <div class="card-body">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tanggal</th>
                      <th>Session</th>
                      <th>Term</th>
                      <th>Class</th>
                      <th>Arm</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  $i = 1;
                  if ($res && mysqli_num_rows($res) > 0):
                      while ($row = mysqli_fetch_assoc($res)):
                          $statusText = $row['status'] == '1' ? 'Hadir' : 'Alfa';
                          $badgeClass = $row['status'] == '1' ? 'badge-success' : 'badge-danger';
                          ?>
                          <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['dateTimeTaken']); ?></td>
                            <td><?php echo htmlspecialchars($row['sessionName']); ?></td>
                            <td><?php echo htmlspecialchars($row['termName']); ?></td>
                            <td><?php echo htmlspecialchars($row['className']); ?></td>
                            <td><?php echo htmlspecialchars($row['classArmName']); ?></td>
                            <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $statusText; ?></span></td>
                          </tr>
                          <?php
                      endwhile;
                  else:
                      ?>
                      <tr>
                        <td colspan="7" class="text-center">Belum ada data absensi.</td>
                      </tr>
                      <?php
                  endif;
                  ?>
                  </tbody>
                </table>
              </div>
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
