<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/Includes/session.php';  // admin guard
require_once __DIR__ . '/../Includes/dbcon.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$teacherId = (int)($_GET['teacherId'] ?? 0);
$msg = "";

// Ambil teacher
$teacher = null;
if ($teacherId > 0) {
  $tq = mysqli_query($conn, "SELECT Id, firstName, lastName, emailAddress FROM tblclassteacher WHERE Id='$teacherId' LIMIT 1");
  if ($tq && mysqli_num_rows($tq) === 1) $teacher = mysqli_fetch_assoc($tq);
}
if (!$teacher) {
  die("Teacher tidak ditemukan.");
}

// ADD assignment
if (isset($_POST['add'])) {
  $classId = trim($_POST['classId'] ?? '');
  $classArmId = trim($_POST['classArmId'] ?? '');

  if ($classId === '' || $classArmId === '') {
    $msg = "<div class='alert alert-danger'>Pilih kelas dan rombel.</div>";
  } else {
    $teacherIdEsc = mysqli_real_escape_string($conn, (string)$teacherId);
    $classIdEsc   = mysqli_real_escape_string($conn, $classId);
    $armIdEsc     = mysqli_real_escape_string($conn, $classArmId);

    $ins = mysqli_query($conn, "
      INSERT IGNORE INTO tblteacher_class (teacherId, classId, classArmId, isActive)
      VALUES ('$teacherIdEsc', '$classIdEsc', '$armIdEsc', 1)
    ");

    if ($ins) {
      $msg = "<div class='alert alert-success'>Assignment ditambahkan.</div>";
    } else {
      $msg = "<div class='alert alert-danger'>Gagal tambah: ".h(mysqli_error($conn))."</div>";
    }
  }
}

// DELETE assignment (hapus pegangan 1 kelas tertentu)
if (isset($_GET['deleteId'])) {
  $deleteId = (int)$_GET['deleteId'];
  if ($deleteId > 0) {
    mysqli_query($conn, "DELETE FROM tblteacher_class WHERE id='$deleteId' AND teacherId='$teacherId'");
  }
  header("Location: teacherAssignments.php?teacherId=".$teacherId);
  exit;
}

// Load dropdown class & arm
$classes = mysqli_query($conn, "SELECT Id, className FROM tblclass ORDER BY className ASC");
$arms    = mysqli_query($conn, "SELECT Id, classArmName FROM tblclassarms ORDER BY classArmName ASC");

// Load assignments teacher
$assign = mysqli_query($conn, "
  SELECT tc.id, tc.classId, tc.classArmId, c.className, a.classArmName, tc.createdAt
  FROM tblteacher_class tc
  INNER JOIN tblclass c ON c.Id = tc.classId
  INNER JOIN tblclassarms a ON a.Id = tc.classArmId
  WHERE tc.teacherId='$teacherId'
  ORDER BY c.className ASC, a.classArmName ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Kelola Pegangan Kelas</title>
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
          <h1 class="h3 mb-0 text-gray-800">Kelola Pegangan Kelas</h1>
          <a href="viewClassTeachers.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <div class="mb-2">
              <strong>Teacher:</strong>
              <?php echo h($teacher['firstName'].' '.$teacher['lastName']); ?>
              <span class="text-muted">(<?php echo h($teacher['emailAddress']); ?>)</span>
            </div>
            <?php echo $msg; ?>

            <form method="post" class="row">
              <div class="col-md-5 mb-2">
                <label>Pilih Kelas</label>
                <select name="classId" class="form-control" required>
                  <option value="">-- Kelas --</option>
                  <?php if ($classes) while($c = mysqli_fetch_assoc($classes)): ?>
                    <option value="<?php echo h($c['Id']); ?>"><?php echo h($c['className']); ?></option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="col-md-5 mb-2">
                <label>Pilih Rombel</label>
                <select name="classArmId" class="form-control" required>
                  <option value="">-- Rombel --</option>
                  <?php if ($arms) while($a = mysqli_fetch_assoc($arms)): ?>
                    <option value="<?php echo h($a['Id']); ?>"><?php echo h($a['classArmName']); ?></option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="col-md-2 mb-2 d-flex align-items-end">
                <button type="submit" name="add" class="btn btn-primary btn-block">
                  <i class="fas fa-plus"></i> Tambah
                </button>
              </div>
            </form>

          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pegangan</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Kelas</th>
                    <th>Rombel</th>
                    <th>Ditambahkan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  $i=1;
                  if ($assign && mysqli_num_rows($assign) > 0):
                    while($r = mysqli_fetch_assoc($assign)):
                ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo h($r['className']); ?></td>
                    <td><?php echo h($r['classArmName']); ?></td>
                    <td><?php echo h($r['createdAt']); ?></td>
                    <td>
                      <a class="btn btn-sm btn-danger"
                         href="teacherAssignments.php?teacherId=<?php echo (int)$teacherId; ?>&deleteId=<?php echo (int)$r['id']; ?>"
                         onclick="return confirm('Hapus pegangan kelas ini?');">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php
                    endwhile;
                  else:
                ?>
                  <tr><td colspan="5" class="text-center">Belum ada pegangan kelas.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>

    <?php include __DIR__ . "/Includes/footer.php"; ?>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
</body>
</html>
