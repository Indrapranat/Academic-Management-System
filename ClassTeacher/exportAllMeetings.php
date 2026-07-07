<?php
require_once __DIR__ . '/Includes/session.php';   // teacher guard
require_once __DIR__ . '/../Includes/dbcon.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$teacherId = (int)($_SESSION['userId'] ?? 0);
if ($teacherId <= 0) {
  header("Location: ../login.php");
  exit;
}

/**
 * Ambil semua kelas yang dipegang teacher dari pivot
 * Return: [ "classId|armId" => ['classId'=>..,'armId'=>..,'label'=>..] ]
 */
$classes = [];
$q = mysqli_query($conn, "
  SELECT tc.classId, tc.classArmId, c.className, a.classArmName
  FROM tblteacher_class tc
  INNER JOIN tblclass c ON c.Id = tc.classId
  INNER JOIN tblclassarms a ON a.Id = tc.classArmId
  WHERE tc.teacherId = '$teacherId' AND tc.isActive = 1
  ORDER BY c.className ASC, a.classArmName ASC
");
if ($q) {
  while ($r = mysqli_fetch_assoc($q)) {
    $key = (int)$r['classId'].'|'.(int)$r['classArmId'];
    $classes[$key] = [
      'classId' => (int)$r['classId'],
      'armId'   => (int)$r['classArmId'],
      'label'   => trim($r['className'].' - '.$r['classArmName']),
    ];
  }
}

if (count($classes) === 0) {
  // teacher belum punya assignment
  $msgNoClass = true;
} else {
  $msgNoClass = false;
}

// ========= EXPORT MODE =========
if (isset($_GET['export']) && $_GET['export'] === '1') {

  $key   = trim($_GET['classKey'] ?? '');
  $from  = trim($_GET['from'] ?? '');
  $to    = trim($_GET['to'] ?? '');

  if ($key === '' || !isset($classes[$key])) {
    die("Invalid class selection.");
  }

  $classId    = (int)$classes[$key]['classId'];
  $classArmId = (int)$classes[$key]['armId'];

  // Filter tanggal optional
  $whereDate = "";
  if ($from !== '' && $to !== '') {
    $fromEsc = mysqli_real_escape_string($conn, $from);
    $toEsc   = mysqli_real_escape_string($conn, $to);
    $whereDate = " AND m.meetingDate BETWEEN '$fromEsc' AND '$toEsc' ";
  } elseif ($from !== '') {
    $fromEsc = mysqli_real_escape_string($conn, $from);
    $whereDate = " AND m.meetingDate >= '$fromEsc' ";
  } elseif ($to !== '') {
    $toEsc = mysqli_real_escape_string($conn, $to);
    $whereDate = " AND m.meetingDate <= '$toEsc' ";
  }

  // Ambil meetings untuk kelas ini
  $classIdEsc = mysqli_real_escape_string($conn, (string)$classId);
  $armIdEsc   = mysqli_real_escape_string($conn, (string)$classArmId);

  $mRes = mysqli_query($conn, "
    SELECT m.Id, m.meetingNo, m.meetingDate, m.meetingTime,
           COALESCE(m.meetingEndTime, '') AS meetingEndTime
    FROM tblmeetings m
    WHERE m.classId = '$classIdEsc'
      AND m.classArmId = '$armIdEsc'
      $whereDate
    ORDER BY m.meetingDate ASC, m.meetingTime ASC
  ");

  // Ambil student list untuk kelas ini
  $sRes = mysqli_query($conn, "
    SELECT Id, fullName, admissionNumber
    FROM tblstudents
    WHERE classId = '$classIdEsc' AND classArmId = '$armIdEsc'
    ORDER BY fullName ASC
  ");

  // Map students
  $students = [];
  if ($sRes) {
    while ($s = mysqli_fetch_assoc($sRes)) {
      $students[] = $s;
    }
  }

  // Map meetings
  $meetings = [];
  if ($mRes) {
    while ($m = mysqli_fetch_assoc($mRes)) {
      $meetings[] = $m;
    }
  }

  // Ambil attendance semua meeting (supaya tidak query per meeting)
  $attendance = []; // [meetingId][admissionNo] = status
  if (count($meetings) > 0) {
    $ids = array_map(fn($x) => (int)$x['Id'], $meetings);
    $in  = implode(',', $ids);

    $aRes = mysqli_query($conn, "
      SELECT meetingId, admissionNo, status
      FROM tblattendance
      WHERE meetingId IN ($in)
        AND classId = '$classIdEsc'
        AND classArmId = '$armIdEsc'
    ");
    if ($aRes) {
      while ($a = mysqli_fetch_assoc($aRes)) {
        $mid = (int)$a['meetingId'];
        $adm = (string)$a['admissionNo'];
        $attendance[$mid][$adm] = (int)$a['status'];
      }
    }
  }

  // Helper status label
  $statusLabel = function(int $s): string {
    if ($s === 1) return 'Hadir';
    if ($s === 2) return 'Izin';
    return 'Alfa';
  };

  // Output XLS (HTML table)
  $title = "Meetings_".$classes[$key]['label'];
  $safeTitle = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $title);

  header("Content-Type: application/vnd.ms-excel; charset=utf-8");
  header("Content-Disposition: attachment; filename={$safeTitle}.xls");
  header("Pragma: no-cache");
  header("Expires: 0");

  echo "<meta charset='utf-8'>";

  echo "<table border='1' cellpadding='5' cellspacing='0'>";
  echo "<tr><th colspan='6' style='font-size:16px;'>Export Meetings - ".h($classes[$key]['label'])."</th></tr>";
  echo "<tr><td colspan='6'>Tanggal Export: ".date('Y-m-d H:i')."</td></tr>";
  echo "<tr><td colspan='6'>Filter Tanggal: ".h($from ?: '-')." s/d ".h($to ?: '-')."</td></tr>";
  echo "</table><br>";

  // Ringkasan per meeting
  echo "<table border='1' cellpadding='5' cellspacing='0'>";
  echo "<tr>
          <th>#</th>
          <th>Pertemuan</th>
          <th>Tanggal</th>
          <th>Jam</th>
          <th>Hadir</th>
          <th>Izin</th>
          <th>Alfa</th>
        </tr>";

  $idx = 1;
  foreach ($meetings as $m) {
    $mid = (int)$m['Id'];
    $hadir = 0; $izin = 0; $alfa = 0;

    foreach ($students as $s) {
      $adm = (string)$s['admissionNumber'];
      $st = $attendance[$mid][$adm] ?? 0;
      if ($st === 1) $hadir++;
      elseif ($st === 2) $izin++;
      else $alfa++;
    }

    $jam = trim($m['meetingTime'].' - '.$m['meetingEndTime']);
    echo "<tr>
            <td>".$idx++."</td>
            <td>".(int)$m['meetingNo']."</td>
            <td>".h($m['meetingDate'])."</td>
            <td>".h($jam)."</td>
            <td>".$hadir."</td>
            <td>".$izin."</td>
            <td>".$alfa."</td>
          </tr>";
  }
  if (count($meetings) === 0) {
    echo "<tr><td colspan='7' align='center'>Tidak ada meeting pada filter yang dipilih.</td></tr>";
  }
  echo "</table><br>";

  // Detail: per student per meeting (pivot)
  echo "<table border='1' cellpadding='5' cellspacing='0'>";
  echo "<tr><th colspan='".(3 + count($meetings))."' style='font-size:14px;'>Detail Kehadiran (Per Siswa)</th></tr>";

  // header
  echo "<tr>
          <th style='width:50px;'>#</th>
          <th>Nama</th>
          <th>NIM</th>";
  foreach ($meetings as $m) {
    $label = "P".$m['meetingNo']." (".$m['meetingDate'].")";
    echo "<th>".h($label)."</th>";
  }
  echo "</tr>";

  $i = 1;
  foreach ($students as $s) {
    echo "<tr>";
    echo "<td>".$i++."</td>";
    echo "<td>".h($s['fullName'])."</td>";
    echo "<td>".h($s['admissionNumber'])."</td>";

    foreach ($meetings as $m) {
      $mid = (int)$m['Id'];
      $adm = (string)$s['admissionNumber'];
      $st  = $attendance[$mid][$adm] ?? 0;
      echo "<td>".h($statusLabel((int)$st))."</td>";
    }

    echo "</tr>";
  }
  if (count($students) === 0) {
    echo "<tr><td colspan='".(3 + count($meetings))."' align='center'>Tidak ada siswa pada kelas ini.</td></tr>";
  }

  echo "</table>";
  exit;
}

// ========= PAGE MODE =========
$selectedKey = $_GET['classKey'] ?? (count($classes) ? array_key_first($classes) : '');
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Export Meetings</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
          <h1 class="h3 mb-0 text-gray-800">Export Meetings (Per Kelas)</h1>
          <a href="meetingList.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
          </a>
        </div>

        <?php if ($msgNoClass): ?>
          <div class="alert alert-danger">
            Teacher ini belum punya pegangan kelas di <b>tblteacher_class</b>.
          </div>
        <?php else: ?>
          <div class="card mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Filter Export</h6>
            </div>
            <div class="card-body">
              <form method="get" class="row">
                <div class="form-group col-md-5">
                  <label>Kelas & Rombel</label>
                  <select name="classKey" class="form-control" required>
                    <option value="">-- pilih kelas --</option>
                    <?php foreach ($classes as $k => $c): ?>
                      <option value="<?= h($k); ?>" <?= ($selectedKey === $k ? 'selected' : ''); ?>>
                        <?= h($c['label']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <small class="text-muted">Export dipisahkan per kelas/rombel.</small>
                </div>

                <div class="form-group col-md-3">
                  <label>Dari Tanggal (opsional)</label>
                  <input type="date" name="from" class="form-control" value="<?= h($from); ?>">
                </div>

                <div class="form-group col-md-3">
                  <label>Sampai Tanggal (opsional)</label>
                  <input type="date" name="to" class="form-control" value="<?= h($to); ?>">
                </div>

                <div class="form-group col-md-12">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Terapkan
                  </button>

                  <a class="btn btn-success"
                     href="?export=1&classKey=<?= h($selectedKey); ?>&from=<?= h($from); ?>&to=<?= h($to); ?>">
                    <i class="fas fa-file-excel"></i> Export Excel
                  </a>
                </div>
              </form>
            </div>
          </div>
        <?php endif; ?>

      </div>

    </div>
    <?php include __DIR__ . '/Includes/footer.php'; ?>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
</body>
</html>
