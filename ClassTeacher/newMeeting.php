<?php
require_once __DIR__ . '/Includes/session.php';
require_once __DIR__ . '/../Includes/dbcon.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$teacherId = (int)($_SESSION['userId'] ?? 0);
$msg = "";

// Ambil semua kelas yg dipegang teacher dari pivot
$classes = [];
if ($teacherId > 0) {
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
      $key = $r['classId'].'|'.$r['classArmId'];
      $classes[$key] = [
        'classId' => $r['classId'],
        'classArmId' => $r['classArmId'],
        'label' => trim($r['className'].' - '.$r['classArmName']),
      ];
    }
  }
}

$selectedKey = $_POST['classKey'] ?? (count($classes) ? array_key_first($classes) : '');

if (isset($_POST['save'])) {
  $meetingNo      = (int)($_POST['meetingNo'] ?? 0);
  $meetingDate    = trim($_POST['meetingDate'] ?? '');
  $meetingTime    = trim($_POST['meetingTime'] ?? '');
  $meetingEndTime = trim($_POST['meetingEndTime'] ?? '');
  $classKey       = trim($_POST['classKey'] ?? '');

  if ($meetingNo <= 0 || $meetingDate === '' || $meetingTime === '') {
    $msg = "<div class='alert alert-danger'>Lengkapi semua field.</div>";
  } elseif ($classKey === '' || !isset($classes[$classKey])) {
    $msg = "<div class='alert alert-danger'>Pilih Kelas & Arm yang valid.</div>";
  } else {
    $classId = $classes[$classKey]['classId'];
    $classArmId = $classes[$classKey]['classArmId'];

    $meetingNoEsc = mysqli_real_escape_string($conn, (string)$meetingNo);
    $classIdEsc   = mysqli_real_escape_string($conn, (string)$classId);
    $armIdEsc     = mysqli_real_escape_string($conn, (string)$classArmId);

    // unik per kelas+arm
    $cek = mysqli_query($conn, "
      SELECT Id FROM tblmeetings
      WHERE meetingNo='$meetingNoEsc' AND classId='$classIdEsc' AND classArmId='$armIdEsc'
      LIMIT 1
    ");
    if ($cek && mysqli_num_rows($cek) > 0) {
      $msg = "<div class='alert alert-danger'>Meeting No $meetingNo sudah digunakan untuk kelas ini.</div>";
    } else {
      $dateEsc = mysqli_real_escape_string($conn, $meetingDate);
      $timeEsc = mysqli_real_escape_string($conn, $meetingTime);

      $endSql = "NULL";
      if ($meetingEndTime !== '') {
        $endEsc = mysqli_real_escape_string($conn, $meetingEndTime);
        $endSql = "'$endEsc'";
      }

      $teacherIdEsc = mysqli_real_escape_string($conn, (string)$teacherId);

      $ins = mysqli_query($conn, "
        INSERT INTO tblmeetings
          (meetingNo, meetingDate, meetingTime, meetingEndTime, classId, classArmId, createdBy)
        VALUES
          ('$meetingNoEsc', '$dateEsc', '$timeEsc', $endSql, '$classIdEsc', '$armIdEsc', '$teacherIdEsc')
      ");

      if ($ins) {
        $_SESSION['msg'] = "Meeting berhasil dibuat untuk ".$classes[$classKey]['label'];
        header("Location: meetingList.php");
        exit;
      } else {
        $msg = "<div class='alert alert-danger'>DB Error: ".h(mysqli_error($conn))."</div>";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add New Meeting</title>
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
          <h1 class="h3 mb-0 text-gray-800">Add New Meeting</h1>
          <a href="meetingList.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
          </a>
        </div>

        <?php echo $msg; ?>

        <?php if (count($classes) === 0): ?>
          <div class="alert alert-danger">
            Teacher ini belum punya assignment kelas. Isi tabel <b>tblteacher_class</b>.
          </div>
        <?php endif; ?>

        <div class="row">
          <div class="col-lg-8">
            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Tambah Pertemuan</h6>
              </div>
              <div class="card-body">
                <form method="post">

                  <div class="form-group">
                    <label>Pertemuan Ke-</label>
                    <input type="number" min="1" required name="meetingNo" class="form-control"
                           placeholder="Contoh: 1 / 2 / 3 ...">
                  </div>

                  <div class="form-group">
                    <label>Kelas & Arm</label>
                    <select name="classKey" class="form-control" required <?php echo count($classes)?'':'disabled'; ?>>
                      <option value="">-- Pilih Kelas --</option>
                      <?php foreach ($classes as $key => $it): ?>
                        <option value="<?php echo h($key); ?>" <?php if ($selectedKey === $key) echo 'selected'; ?>>
                          <?php echo h($it['label']); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>Tanggal Pertemuan</label>
                    <input type="date" required name="meetingDate" class="form-control">
                  </div>

                  <div class="form-group">
                    <label>Jam Mulai Pertemuan</label>
                    <input type="time" required name="meetingTime" class="form-control">
                  </div>

                  <div class="form-group">
                    <label>Jam Batas Absen (Opsional)</label>
                    <input type="time" name="meetingEndTime" class="form-control">
                  </div>

                  <button type="submit" name="save" class="btn btn-primary" <?php echo count($classes)?'':'disabled'; ?>>
                    <i class="fas fa-save"></i> Simpan Meeting
                  </button>

                </form>
              </div>
            </div>
          </div>
        </div>

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
