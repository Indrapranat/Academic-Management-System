<?php
require_once __DIR__ . '/Includes/session.php';   // ✅ teacher guard (bootRoleSession teacher)
require_once __DIR__ . '/../Includes/dbcon.php';  // ✅ DB

// Pastikan role guru
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit;
}

$classId    = $_SESSION['classId'] ?? 0;
$classArmId = $_SESSION['classArmId'] ?? 0;

if ($classId <= 0 || $classArmId <= 0) {
    die("Class context not found in session.");
}

// ------------------------------------------------------
// Hapus meeting (optional, via ?delete=ID)
// ------------------------------------------------------
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($delId > 0) {
        // hapus dulu kehadiran
        mysqli_query($conn, "DELETE FROM tblattendance WHERE meetingId = '$delId'");
        // hapus meeting
        mysqli_query($conn, "DELETE FROM tblmeetings WHERE Id = '$delId' AND classId='$classId' AND classArmId='$classArmId'");
        $_SESSION['meeting_msg'] = "Meeting berhasil dihapus.";
    }
    header("Location: meetingList.php");
    exit;
}

// ------------------------------------------------------
// Ambil list meeting + rekap hadir
// ------------------------------------------------------
// totalStudent = jumlah siswa di kelas
$studentRes = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS totalStudent
     FROM tblstudents
     WHERE classId = '$classId' AND classArmId = '$classArmId'"
);
$studentRow    = $studentRes ? mysqli_fetch_assoc($studentRes) : ['totalStudent' => 0];
$totalStudents = (int)$studentRow['totalStudent'];

// Meeting + rekap attendance per meeting
$meetingRes = mysqli_query(
    $conn,
    "SELECT 
        m.*,
        COALESCE(SUM(CASE WHEN a.status = 1 THEN 1 ELSE 0 END),0) AS totalPresent,
        COALESCE(COUNT(a.Id),0) AS totalRecorded
     FROM tblmeetings m
     LEFT JOIN tblattendance a 
        ON a.meetingId = m.Id
       AND a.classId   = '$classId'
       AND a.classArmId = '$classArmId'
     WHERE m.classId   = '$classId'
       AND m.classArmId = '$classArmId'
     GROUP BY m.Id
     ORDER BY m.meetingDate DESC, m.meetingTime DESC"
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Meeting List</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

  <?php include "Includes/sidebar.php"; ?>

  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

      <?php include "Includes/topbar.php"; ?>

      <div class="container-fluid" id="container-wrapper">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Daftar Pertemuan</h1>
          <a href="newMeeting.php" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Buat Pertemuan Baru
          </a>
        </div>

        <?php
        if (!empty($_SESSION['meeting_msg'])) {
            echo '<div class="alert alert-info">'.htmlspecialchars($_SESSION['meeting_msg']).'</div>';
            unset($_SESSION['meeting_msg']);
        }
        ?>

        <div class="card mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Meeting List (kelas Anda)</h6>
          </div>
          <div class="card-body">

            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Pertemuan</th>
                    <th>Tanggal</th>
                    <th>Waktu Absen</th>
                    <th>Hadir / Total</th>
                    <th>Persentase</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  if ($meetingRes && mysqli_num_rows($meetingRes) > 0):
                      while ($m = mysqli_fetch_assoc($meetingRes)):
                          $present = (int)$m['totalPresent'];
                          // pakai totalStudents sebagai baseline persentase
                          $percent = $totalStudents > 0
                              ? round(($present / $totalStudents) * 100, 1)
                              : 0.0;
                  ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo (int)$m['meetingNo']; ?></td>
                    <td><?php echo htmlspecialchars($m['meetingDate']); ?></td>
                    <td>
                      <?php echo htmlspecialchars($m['meetingTime']); ?>
                      &ndash;
                      <?php echo htmlspecialchars($m['cutoffTime']); ?>
                    </td>
                    <td>
                      <?php echo $present; ?> /
                      <?php echo $totalStudents; ?>
                    </td>
                    <td><?php echo $percent; ?>%</td>
                    <td>
                      <a href="viewMeeting.php?id=<?php echo (int)$m['Id']; ?>"
                         class="btn btn-sm btn-info">
                         <i class="fas fa-eye"></i> View
                      </a>
                      <a href="exportMeetingExcel.php?id=<?php echo (int)$m['Id']; ?>"
                         class="btn btn-sm btn-success">
                         <i class="fas fa-file-excel"></i>
                      </a>
                      <a href="meetingList.php?delete=<?php echo (int)$m['Id']; ?>"
                         class="btn btn-sm btn-danger"
                         onclick="return confirm('Hapus pertemuan ini beserta data absensinya?');">
                         <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                  <?php
                      endwhile;
                  else:
                  ?>
                  <tr>
                    <td colspan="7" class="text-center">Belum ada pertemuan untuk kelas ini.</td>
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
</body>
</html>
