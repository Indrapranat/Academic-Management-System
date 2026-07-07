<?php
require_once __DIR__ . '/../Includes/session_bootstrap.php';
bootRoleSession('student');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

include '../Includes/dbcon.php';
include 'includes/session.php';

$studentId   = $_SESSION['studentId'];
$admissionNo = $_SESSION['admissionNumber'];
$classId     = $_SESSION['classId'];
$classArmId  = $_SESSION['classArmId'];

$todayDate = date('Y-m-d');

// ------------------ DATA SISWA ------------------
$q = mysqli_query(
    $conn,
    "SELECT s.fullName, s.admissionNumber, c.className, a.classArmName
     FROM tblstudents s
     JOIN tblclass c ON c.Id = s.classId
     JOIN tblclassarms a ON a.Id = s.classArmId
     WHERE s.Id = '$studentId'"
);
$student = mysqli_fetch_assoc($q);

// ------------------ MEETING HARI INI ------------------
// *** FIX UTAMA → gunakan DATE(meetingDate) agar meeting tampil ***
$meetingSql = "
    SELECT Id, meetingNo, meetingDate, meetingTime, meetingEndTime
    FROM tblmeetings
    WHERE classId    = '$classId'
      AND classArmId = '$classArmId'
      AND DATE(meetingDate) = '$todayDate'
    ORDER BY meetingTime ASC
";
$todayMeetingsRes = mysqli_query($conn, $meetingSql);

$todayMeetings = [];
if ($todayMeetingsRes && mysqli_num_rows($todayMeetingsRes) > 0) {
    while ($m = mysqli_fetch_assoc($todayMeetingsRes)) {
        $mid = $m['Id'];

        $attRes = mysqli_query($conn, "
            SELECT status, dateTimeTaken
            FROM tblattendance
            WHERE admissionNo = '$admissionNo'
              AND classId     = '$classId'
              AND classArmId  = '$classArmId'
              AND meetingId   = '$mid'
            LIMIT 1
        ");

        $m['hasAttended'] = false;
        $m['attendanceRow'] = null;

        if ($attRes && mysqli_num_rows($attRes) === 1) {
            $m['hasAttended']   = true;
            $m['attendanceRow'] = mysqli_fetch_assoc($attRes);
        }

        $todayMeetings[] = $m;
    }
}

// ------------------ MEETING UNTUK LIHAT TEMAN ------------------
$selectedMeetingId = isset($_GET['meeting']) ? (int)$_GET['meeting'] : null;
$selectedMeeting   = null;
$friendsRes        = null;

if ($selectedMeetingId) {
    $mRes = mysqli_query($conn, "
        SELECT Id, meetingNo, meetingDate, meetingTime
        FROM tblmeetings
        WHERE Id = '$selectedMeetingId'
          AND classId    = '$classId'
          AND classArmId = '$classArmId'
        LIMIT 1
    ");
    if ($mRes && mysqli_num_rows($mRes) === 1) {
        $selectedMeeting = mysqli_fetch_assoc($mRes);

        $friendsSql = "
            SELECT s.fullName, a.status, a.dateTimeTaken
            FROM tblattendance a
            JOIN tblstudents s ON s.admissionNumber = a.admissionNo
            WHERE a.classId    = '$classId'
              AND a.classArmId = '$classArmId'
              AND a.meetingId  = '$selectedMeetingId'
            ORDER BY s.fullName ASC
        ";
        $friendsRes = mysqli_query($conn, $friendsSql);
    }
}

// ------------------ RINGKASAN HADIR / ALFA ------------------
$statRes = mysqli_query(
    $conn,
    "SELECT 
        SUM(status = '1') AS totalPresent,
        SUM(status = '0') AS totalAbsent
     FROM tblattendance
     WHERE admissionNo = '$admissionNo'
       AND classId     = '$classId'
       AND classArmId  = '$classArmId'"
);
$stat = $statRes ? mysqli_fetch_assoc($statRes) : ['totalPresent'=>0,'totalAbsent'=>0];

// ------------------ RIWAYAT ABSEN TERAKHIR ------------------
$historyRes = mysqli_query(
    $conn,
    "SELECT dateTimeTaken, status
     FROM tblattendance
     WHERE admissionNo = '$admissionNo'
       AND classId     = '$classId'
       AND classArmId  = '$classArmId'
     ORDER BY dateTimeTaken DESC
     LIMIT 10"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Dashboard</title>
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

      <div class="container-fluid">

        <h1 class="h3 mb-4">Student Dashboard</h1>

        <?php if (isset($_SESSION['attendance_msg'])): ?>
          <div class="alert alert-info"><?php echo $_SESSION['attendance_msg']; ?></div>
          <?php unset($_SESSION['attendance_msg']); ?>
        <?php endif; ?>

        <div class="row">
          <!-- Info Siswa -->
          <div class="col-md-4">
            <div class="card mb-3">
              <div class="card-body">
                <h5><?php echo htmlspecialchars($student['fullName']); ?></h5>
                <p>NIM: <?php echo htmlspecialchars($student['admissionNumber']); ?></p>
                <p>Kelas: <?php echo htmlspecialchars($student['className'] . ' - ' . $student['classArmName']); ?></p>
              </div>
            </div>
          </div>

          <!-- Ringkasan -->
          <div class="col-md-4">
            <div class="card mb-3">
              <div class="card-body">
                <h6>Ringkasan Kehadiran</h6>
                <p>Hadir: <b><?php echo (int)$stat['totalPresent']; ?></b></p>
                <p>Alfa:  <b><?php echo (int)$stat['totalAbsent']; ?></b></p>
              </div>
            </div>
          </div>

          <!-- Link ke Riwayat -->
          <div class="col-md-4">
            <div class="card mb-3">
              <div class="card-body text-center">
                <a href="attendance-history.php" class="btn btn-primary btn-block">Lihat Riwayat Lengkap</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Daftar Meeting Hari Ini -->
        <div class="card mb-4">
          <div class="card-header">
            Pertemuan Hari Ini (<?php echo $todayDate; ?>)
          </div>
          <div class="card-body">
            <?php if (count($todayMeetings) === 0): ?>
              <div class="alert alert-info mb-0">
                Belum ada meeting yang terjadwal hari ini untuk kelas Anda.
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Pertemuan</th>
                      <th>Waktu</th>
                      <th>Status Saya</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  $i = 1;
                  foreach ($todayMeetings as $m):
                      $hasAtt = $m['hasAttended'];
                      $rowAtt = $m['attendanceRow'];
                      $badge  = $hasAtt ? 'success' : 'danger';
                      $text   = $hasAtt ? 'Sudah Hadir' : 'Belum Absen';
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td>Pertemuan <?php echo (int)$m['meetingNo']; ?></td>
                      <td>
                        <?php echo htmlspecialchars($m['meetingTime']); ?>
                        <?php if (!empty($m['meetingEndTime']) && $m['meetingEndTime'] != '00:00:00'): ?>
                          - <?php echo htmlspecialchars($m['meetingEndTime']); ?>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="badge badge-<?php echo $badge; ?>">
                          <?php echo $text; ?>
                        </span>
                        <?php if ($hasAtt && $rowAtt): ?>
                          <div style="font-size: 0.8rem;">
                            <?php echo date('d M Y H:i', strtotime($rowAtt['dateTimeTaken'])); ?>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (!$hasAtt): ?>
                          <form method="post" action="attendance.php" class="d-inline">
                            <input type="hidden" name="meetingId" value="<?php echo (int)$m['Id']; ?>">
                            <button type="submit" name="present" class="btn btn-success btn-sm">
                              Absen
                            </button>
                          </form>
                        <?php endif; ?>

                        <a href="index.php?meeting=<?php echo (int)$m['Id']; ?>#teman"
                           class="btn btn-info btn-sm">
                          Lihat Teman
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Teman Hadir -->
        <div class="card mb-4" id="teman">
          <div class="card-header">Teman yang sudah hadir</div>
          <div class="card-body">
            <?php if (!$selectedMeeting): ?>
              <p class="text-muted mb-0">
                Pilih tombol <b>"Lihat Teman"</b> pada salah satu pertemuan di atas untuk melihat daftar kehadiran.
              </p>
            <?php else: ?>
              <h6>
                Pertemuan ke-<?php echo (int)$selectedMeeting['meetingNo']; ?>,
                Tanggal <?php echo htmlspecialchars($selectedMeeting['meetingDate']); ?>,
                Jam <?php echo htmlspecialchars($selectedMeeting['meetingTime']); ?>
              </h6>
              <hr>

              <?php if ($friendsRes && mysqli_num_rows($friendsRes) > 0): ?>
                <div class="table-responsive">
                  <table class="table table-sm table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Waktu Absen</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                    $n = 1;
                    while ($f = mysqli_fetch_assoc($friendsRes)):
                        $stText  = $f['status'] == '1' ? 'Hadir' : 'Alfa';
                        $stBadge = $f['status'] == '1' ? 'success' : 'danger';
                    ?>
                      <tr>
                        <td><?php echo $n++; ?></td>
                        <td><?php echo htmlspecialchars($f['fullName']); ?></td>
                        <td><span class="badge badge-<?php echo $stBadge; ?>"><?php echo $stText; ?></span></td>
                        <td><?php echo htmlspecialchars($f['dateTimeTaken']); ?></td>
                      </tr>
                    <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <p class="mb-0">Belum ada teman yang absen untuk pertemuan ini.</p>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- History -->
        <div class="card mb-3">
          <div class="card-header">Riwayat Terakhir</div>
          <div class="card-body">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Tanggal & Waktu</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $i = 1;
                if ($historyRes && mysqli_num_rows($historyRes) > 0):
                    while ($row = mysqli_fetch_assoc($historyRes)):
                        $status = $row['status'] == '1' ? 'Hadir' : 'Alfa';
                        $color  = $row['status'] == '1' ? 'success' : 'danger';
                ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo htmlspecialchars($row['dateTimeTaken']); ?></td>
                  <td><span class="badge badge-<?php echo $color; ?>"><?php echo $status; ?></span></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="3" class="text-center">Belum ada data absensi.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>

</body>
</html>
