<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

/**
 * 1) TABEL UTAMA: SEMUA SISWA
 */
$qAll = "
    SELECT 
        s.Id,
        s.fullName,
        s.admissionNumber,
        c.className,
        a.classArmName
    FROM tblstudents s
    JOIN tblclass     c ON c.Id = s.classId
    JOIN tblclassarms a ON a.Id = s.classArmId
    ORDER BY s.fullName ASC
";
$resAll = mysqli_query($conn, $qAll);
if (!$resAll) {
    die('Query Error (all students): ' . mysqli_error($conn));
}

/**
 * 2) DATA PER KELAS/ROMBEL + WALI KELAS
 *    - semua kombinasi class + arm
 *    - join wali kelas kalau ada
 */
$qClassArm = "
    SELECT 
        c.Id  AS classId,
        c.className,
        a.Id  AS classArmId,
        a.classArmName,
        CONCAT(IFNULL(t.firstName,''), ' ', IFNULL(t.lastName,'')) AS teacherName
    FROM tblclassarms a
    JOIN tblclass c ON c.Id = a.classId
    LEFT JOIN tblclassteacher t 
        ON t.classId = c.Id AND t.classArmId = a.Id
    ORDER BY c.className ASC, a.classArmName ASC
";
$classArmRes = mysqli_query($conn, $qClassArm);
if (!$classArmRes) {
    die('Query Error (class/arm list): ' . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Daftar Siswa</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

  <?php include "Includes/sidebar.php"; ?>

  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

      <?php include "Includes/topbar.php"; ?>

      <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Daftar Siswa</h1>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Daftar Siswa</li>
          </ol>
        </div>

        <!-- ==========================================================
             BAGIAN 1: TABEL SEMUA SISWA
        =========================================================== -->
        <div class="card mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Semua Siswa</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive p-3">
              <table class="table table-bordered table-hover" id="dataTableStudents" width="100%" cellspacing="0">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Kelas</th>
                    <th>Arm</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  while ($row = mysqli_fetch_assoc($resAll)): ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= htmlspecialchars($row['fullName']); ?></td>
                      <td><?= htmlspecialchars($row['admissionNumber']); ?></td>
                      <td><?= htmlspecialchars($row['className']); ?></td>
                      <td><?= htmlspecialchars($row['classArmName']); ?></td>
                      <td>
                        <a href="transferStudent.php?id=<?= (int)$row['Id']; ?>" 
                           class="btn btn-warning btn-sm">
                          Pindah Kelas
                        </a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ==========================================================
             BAGIAN 2: RINGKASAN PER KELAS + NESTED TABLE SISWA
        =========================================================== -->
        <div class="card mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
              Siswa per Kelas & Wali Kelas
            </h6>
          </div>
          <div class="card-body">

            <?php
            // loop setiap kombinasi Kelas + Arm
            while ($classRow = mysqli_fetch_assoc($classArmRes)):

                $classId    = (int)$classRow['classId'];
                $classArmId = (int)$classRow['classArmId'];

                // ambil siswa untuk kelas+arm ini
                $stuRes = mysqli_query($conn, "
                    SELECT Id, fullName, admissionNumber
                    FROM tblstudents
                    WHERE classId = '$classId'
                      AND classArmId = '$classArmId'
                    ORDER BY fullName ASC
                ");
                if (!$stuRes) {
                    continue; // skip kalau error
                }

                $hasStudents = mysqli_num_rows($stuRes) > 0;
            ?>

            <div class="mb-4 border rounded p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                  <h6 class="mb-1">
                    Kelas: <strong><?= htmlspecialchars($classRow['className']); ?></strong>
                    &nbsp; | &nbsp;
                    Arm: <strong><?= htmlspecialchars($classRow['classArmName']); ?></strong>
                  </h6>
                  <small class="text-muted">
                    Wali Kelas: 
                    <strong>
                      <?= trim($classRow['teacherName']) !== '' 
                           ? htmlspecialchars($classRow['teacherName']) 
                           : 'Belum ditetapkan'; ?>
                    </strong>
                  </small>
                </div>
                <?php if ($hasStudents): ?>
                  <span class="badge badge-info">
                    <?= mysqli_num_rows($stuRes); ?> siswa
                  </span>
                <?php else: ?>
                  <span class="badge badge-secondary">
                    Tidak ada siswa
                  </span>
                <?php endif; ?>
              </div>

              <?php if ($hasStudents): ?>
                <div class="table-responsive">
                  <table class="table table-sm table-bordered mb-0">
                    <thead class="thead-light">
                      <tr>
                        <th style="width:50px;">#</th>
                        <th>Nama Siswa</th>
                        <th>NIM</th>
                        <th style="width:120px;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $no = 1;
                      while ($stu = mysqli_fetch_assoc($stuRes)): ?>
                        <tr>
                          <td><?= $no++; ?></td>
                          <td><?= htmlspecialchars($stu['fullName']); ?></td>
                          <td><?= htmlspecialchars($stu['admissionNumber']); ?></td>
                          <td>
                            <a href="transferStudent.php?id=<?= (int)$stu['Id']; ?>"
                               class="btn btn-sm btn-warning">
                              Pindah Kelas
                            </a>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <p class="mb-0 text-muted">Belum ada siswa di kelas ini.</p>
              <?php endif; ?>

            </div>

            <?php endwhile; ?>

          </div>
        </div>

      </div><!-- /.container-fluid -->
    </div>

    <?php include "Includes/footer.php"; ?>

  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
<script>
  $(document).ready(function () {
    $('#dataTableStudents').DataTable();
  });
</script>
</body>
</html>
