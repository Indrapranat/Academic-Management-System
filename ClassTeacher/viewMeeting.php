<?php
require_once __DIR__ . '/Includes/session.php';   // WAJIB: teacher guard dulu
require_once __DIR__ . '/../Includes/dbcon.php';  // DB setelah session aman

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$meetingId  = (int)($_GET['id'] ?? 0);
$classId    = (int)($_SESSION['classId'] ?? 0);
$classArmId = (int)($_SESSION['classArmId'] ?? 0);

if ($meetingId <= 0 || $classId <= 0 || $classArmId <= 0) {
    http_response_code(400);
    die("Invalid meeting or class context.");
}

// Ambil info meeting (pastikan field end time kamu konsisten)
// Di kode kamu: insert pakai meetingEndTime, tapi tampilan pakai cutoffTime -> mismatch.
// Saya pakai meetingEndTime.
$mRes = mysqli_query($conn, "
    SELECT Id, meetingNo, meetingDate, meetingTime, meetingEndTime
    FROM tblmeetings
    WHERE Id = '$meetingId'
      AND classId = '$classId'
      AND classArmId = '$classArmId'
    LIMIT 1
");
$m = $mRes ? mysqli_fetch_assoc($mRes) : null;

if (!$m) {
    http_response_code(404);
    die("Meeting not found.");
}

// List student
$students = mysqli_query($conn, "
    SELECT Id, fullName, admissionNumber
    FROM tblstudents
    WHERE classId = '$classId'
      AND classArmId = '$classArmId'
    ORDER BY fullName ASC
");

// Attendance per meeting
$att = mysqli_query($conn, "
    SELECT admissionNo, status
    FROM tblattendance
    WHERE meetingId = '$meetingId'
");

// Map: [admissionNo => status]
$attendance = [];
if ($att) {
    while ($a = mysqli_fetch_assoc($att)) {
        $attendance[$a['admissionNo']] = (int)$a['status'];
    }
}

$totalStudent = $students ? mysqli_num_rows($students) : 0;
$totalPresent = 0;
foreach ($attendance as $st) {
    if ((int)$st === 1) $totalPresent++;
}
$percent = $totalStudent > 0 ? round(($totalPresent / $totalStudent) * 100, 1) : 0.0;

function status_label(int $s): string {
    if ($s === 1) return 'Hadir';
    if ($s === 2) return 'Izin';
    return 'Alfa';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>View Meeting</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

  <?php include __DIR__ . "/Includes/sidebar.php"; ?>

  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

      <?php include __DIR__ . "/Includes/topbar.php"; ?>

      <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-2">
          <h3 class="mb-0">
            Pertemuan <?php echo (int)$m['meetingNo']; ?> –
            <?php echo h($m['meetingDate']); ?>
          </h3>

          <a class="btn btn-secondary btn-sm" href="meetingList.php">
            <i class="fas fa-arrow-left"></i> Back
          </a>
        </div>

        <p class="text-muted mb-3">
          Waktu: <?php echo h($m['meetingTime']); ?>
          s/d <?php echo h($m['meetingEndTime'] ?? ''); ?>
        </p>

        <div class="mb-3">
          <strong>Total hadir:</strong>
          <?php echo (int)$totalPresent; ?> /
          <?php echo (int)$totalStudent; ?>
          (<?php echo h($percent); ?>%)
        </div>

        <a href="exportMeetingexcel.php?id=<?php echo (int)$meetingId; ?>"
           class="btn btn-success btn-sm mb-3">
          <i class="fas fa-file-excel"></i> Export Excel
        </a>

        <div class="card mb-4">
          <div class="card-body">

            <table class="table table-bordered table-hover">
              <thead class="thead-light">
                <tr>
                  <th style="width:50px;">#</th>
                  <th>Nama</th>
                  <th style="width:160px;">Admission No</th>
                  <th style="width:220px;">Status</th>
                </tr>
              </thead>

              <tbody>
              <?php
              $i = 1;
              if ($students && mysqli_num_rows($students) > 0):
                  mysqli_data_seek($students, 0);
                  while ($s = mysqli_fetch_assoc($students)):
                      $adm = (string)$s['admissionNumber'];
                      $status = isset($attendance[$adm]) ? (int)$attendance[$adm] : 0;
              ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo h($s['fullName']); ?></td>
                  <td><?php echo h($adm); ?></td>
                  <td>
                    <form method="post" action="updateAttendance.php" class="form-inline mb-0">
                      <input type="hidden" name="meetingId" value="<?php echo (int)$meetingId; ?>">
                      <input type="hidden" name="admissionNo" value="<?php echo h($adm); ?>">

                      <select name="status" class="form-control form-control-sm mr-2"
                              onchange="this.form.submit()">
                        <option value="1" <?php if ($status === 1) echo 'selected'; ?>>Hadir</option>
                        <option value="0" <?php if ($status === 0) echo 'selected'; ?>>Alfa</option>
                        <option value="2" <?php if ($status === 2) echo 'selected'; ?>>Izin</option>
                      </select>

                      <?php
                      if ($status === 1) {
                          echo '<span class="badge badge-success">Hadir</span>';
                      } elseif ($status === 2) {
                          echo '<span class="badge badge-warning">Izin</span>';
                      } else {
                          echo '<span class="badge badge-danger">Alfa</span>';
                      }
                      ?>
                    </form>
                  </td>
                </tr>
              <?php
                  endwhile;
              else:
              ?>
                <tr>
                  <td colspan="4" class="text-center">Tidak ada data siswa.</td>
                </tr>
              <?php endif; ?>
              </tbody>

            </table>

          </div>
        </div>

      </div>
    </div>

    <?php include __DIR__ . "/Includes/footer.php"; ?>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
