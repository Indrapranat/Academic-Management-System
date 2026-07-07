<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$teacherId  = (int)($_SESSION['userId'] ?? 0);
$classId    = (int)($_SESSION['classId'] ?? 0);
$classArmId = (int)($_SESSION['classArmId'] ?? 0);

$studentId = (int)($_GET['id'] ?? 0);

if ($teacherId <= 0 || $studentId <= 0) {
    die("Invalid request.");
}

// Ambil data siswa, tapi pastikan di kelas teacher
$sRes = mysqli_query($conn, "
    SELECT *
    FROM tblstudents
    WHERE Id = '$studentId'
      AND classId = '$classId'
      AND classArmId = '$classArmId'
    LIMIT 1
");
$student = $sRes ? mysqli_fetch_assoc($sRes) : null;

if (!$student) {
    die("Siswa tidak ditemukan di kelas Anda.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName        = trim($_POST['fullName'] ?? '');
    $admissionNumber = trim($_POST['admissionNumber'] ?? '');
    $password        = trim($_POST['password'] ?? '');

    if ($fullName === '' || $admissionNumber === '') {
        $error = 'Nama dan NIM wajib diisi.';
    } else {
        // cek NIM tidak duplikat ke siswa lain
        $admEsc = mysqli_real_escape_string($conn, $admissionNumber);
        $cek = mysqli_query($conn, "
            SELECT Id FROM tblstudents
            WHERE admissionNumber = '$admEsc'
              AND Id <> '$studentId'
            LIMIT 1
        ");

        if ($cek && mysqli_num_rows($cek) > 0) {
            $error = 'Admission Number/NIM sudah digunakan siswa lain.';
        } else {
            $fullNameEsc = mysqli_real_escape_string($conn, $fullName);

            $setPassword = "";
            if ($password !== '') {
                $passEsc = mysqli_real_escape_string($conn, $password);
                $setPassword = ", password = '$passEsc'";
            }

            $upd = mysqli_query($conn, "
                UPDATE tblstudents
                SET fullName = '$fullNameEsc',
                    admissionNumber = '$admEsc'
                    $setPassword
                WHERE Id = '$studentId'
                LIMIT 1
            ");

            if ($upd) {
                $success = 'Data siswa berhasil diperbarui.';
                // refresh data
                $sRes2 = mysqli_query($conn, "
                    SELECT * FROM tblstudents
                    WHERE Id = '$studentId' LIMIT 1
                ");
                $student = $sRes2 ? mysqli_fetch_assoc($sRes2) : $student;
            } else {
                $error = 'Gagal menyimpan: '.mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Siswa</title>
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
          <h1 class="h3 mb-0 text-gray-800">Edit Siswa</h1>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="viewStudents.php">Students</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
            <form method="post">
              <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="fullName" class="form-control"
                       value="<?= htmlspecialchars($student['fullName']); ?>" required>
              </div>
              <div class="form-group">
                <label>NIM / Admission Number</label>
                <input type="text" name="admissionNumber" class="form-control"
                       value="<?= htmlspecialchars($student['admissionNumber']); ?>" required>
              </div>
              <div class="form-group">
                <label>Password (isi jika mau ganti)</label>
                <input type="text" name="password" class="form-control" placeholder="kosongkan jika tidak diubah">
              </div>

              <button type="submit" class="btn btn-primary">Simpan</button>
              <a href="viewStudents.php" class="btn btn-secondary">Kembali</a>
            </form>
          </div>
        </div>

      </div>
    </div>
    <?php include 'Includes/footer.php'; ?>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
</body>
</html>
