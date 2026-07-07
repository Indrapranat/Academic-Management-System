<?php
// File: Admin/transferStudent.php

include '../Includes/dbcon.php';
include '../Includes/session.php';

$studentId = (int)($_GET['id'] ?? 0);
if ($studentId <= 0) {
    die("Student tidak valid.");
}

// Ambil data siswa sekarang
$sRes = mysqli_query($conn, "
    SELECT s.*, c.className, a.classArmName
    FROM tblstudents s
    LEFT JOIN tblclass     c ON c.Id = s.classId
    LEFT JOIN tblclassarms a ON a.Id = s.classArmId
    WHERE s.Id = '$studentId'
    LIMIT 1
");
$student = $sRes ? mysqli_fetch_assoc($sRes) : null;
if (!$student) {
    die("Student tidak ditemukan.");
}

// Ambil semua class
$classRes = mysqli_query($conn, "
    SELECT Id, className 
    FROM tblclass 
    ORDER BY className ASC
");

// Ambil semua arm (dengan info class)
$armRes = mysqli_query($conn, "
    SELECT a.Id, a.classArmName, a.classId, c.className
    FROM tblclassarms a
    JOIN tblclass c ON c.Id = a.classId
    ORDER BY c.className ASC, a.classArmName ASC
");
$arms = [];
if ($armRes) {
    while ($r = mysqli_fetch_assoc($armRes)) {
        $arms[] = $r;
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newClassId = (int)($_POST['classId'] ?? 0);
    $newArmId   = (int)($_POST['classArmId'] ?? 0);

    if ($newClassId <= 0 || $newArmId <= 0) {
        $error = "Class dan Class Arm wajib dipilih.";
    } else {
        // pastikan arm milik class tersebut
        $cekArm = mysqli_query($conn, "
            SELECT Id FROM tblclassarms
            WHERE Id = '$newArmId' AND classId = '$newClassId'
            LIMIT 1
        ");
        if (!$cekArm || mysqli_num_rows($cekArm) === 0) {
            $error = "Kombinasi Class & Arm tidak valid.";
        } else {
            $upd = mysqli_query($conn, "
                UPDATE tblstudents
                SET classId = '$newClassId',
                    classArmId = '$newArmId'
                WHERE Id = '$studentId'
                LIMIT 1
            ");

            if ($upd) {
                $success = "Siswa berhasil dipindahkan ke kelas baru.";

                // refresh info siswa
                $sRes2 = mysqli_query($conn, "
                    SELECT s.*, c.className, a.classArmName
                    FROM tblstudents s
                    LEFT JOIN tblclass     c ON c.Id = s.classId
                    LEFT JOIN tblclassarms a ON a.Id = s.classArmId
                    WHERE s.Id = '$studentId'
                    LIMIT 1
                ");
                $student = $sRes2 ? mysqli_fetch_assoc($sRes2) : $student;
            } else {
                $error = "Gagal menyimpan: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Pindah Kelas Siswa</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">
  <?php include 'Includes/sidebar.php'; ?>
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
      <?php include 'Includes/topbar.php'; ?>

      <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Pindah Kelas Siswa</h1>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="viewStudents.php">Students</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pindah Kelas</li>
          </ol>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
          <div class="card-body">
            <p><strong>Nama:</strong> <?= htmlspecialchars($student['fullName']); ?></p>
            <p><strong>NIM:</strong> <?= htmlspecialchars($student['admissionNumber']); ?></p>
            <p>
              <strong>Kelas sekarang:</strong>
              <?= htmlspecialchars($student['className'] . ' - ' . $student['classArmName']); ?>
            </p>
            <hr>

            <form method="post">
              <div class="form-group">
                <label>Class Baru</label>
                <select name="classId" class="form-control" required>
                  <option value="">-- pilih class --</option>
                  <?php while ($c = mysqli_fetch_assoc($classRes)): ?>
                    <option value="<?= (int)$c['Id']; ?>">
                      <?= htmlspecialchars($c['className']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="form-group">
                <label>Class Arm Baru</label>
                <select name="classArmId" class="form-control" required>
                  <option value="">-- pilih arm --</option>
                  <?php foreach ($arms as $a): ?>
                    <option value="<?= (int)$a['Id']; ?>">
                      <?= htmlspecialchars($a['className'] . ' - ' . $a['classArmName']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <small class="text-muted">
                  Pilih Arm yang sesuai dengan Class yang di atas.
                </small>
              </div>

              <button type="submit" class="btn btn-primary">Simpan</button>
              <a href="viewStudents.php" class="btn btn-secondary">Kembali</a>
            </form>
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
