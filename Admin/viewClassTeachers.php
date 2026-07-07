<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$flash = "";

/* =========================
   HANDLE UPDATE TEACHER
========================= */
if (isset($_POST['updateTeacher'])) {
    $tid = (int)($_POST['teacherId'] ?? 0);
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $email     = trim($_POST['emailAddress'] ?? '');
    $newPass   = (string)($_POST['newPassword'] ?? '');

    if ($tid <= 0 || $firstName === '' || $lastName === '' || $email === '') {
        $flash = "<div class='alert alert-danger'>Lengkapi Firstname, Lastname, dan Email.</div>";
    } else {
        $firstEsc = mysqli_real_escape_string($conn, $firstName);
        $lastEsc  = mysqli_real_escape_string($conn, $lastName);
        $emailEsc = mysqli_real_escape_string($conn, $email);

        // optional password update
        $passSql = "";
        if ($newPass !== '') {
            $passHash = md5($newPass);
            $passSql = ", password='".mysqli_real_escape_string($conn, $passHash)."'";
        }

        $upd = mysqli_query($conn, "
            UPDATE tblclassteacher
            SET firstName='$firstEsc',
                lastName='$lastEsc',
                emailAddress='$emailEsc'
                $passSql
            WHERE Id='$tid'
            LIMIT 1
        ");

        if ($upd) {
            $flash = "<div class='alert alert-success'>Data teacher berhasil diupdate.</div>";
        } else {
            $flash = "<div class='alert alert-danger'>Gagal update: ".h(mysqli_error($conn))."</div>";
        }
    }
}

/* =========================
   HANDLE DELETE TEACHER
========================= */
if (isset($_POST['deleteTeacher'])) {
    $tid = (int)($_POST['teacherId'] ?? 0);
    if ($tid <= 0) {
        $flash = "<div class='alert alert-danger'>Teacher tidak valid.</div>";
    } else {
        // Hapus assignment dulu biar bersih
        mysqli_query($conn, "DELETE FROM tblteacher_class WHERE teacherId='$tid'");

        // Hapus teacher
        $del = mysqli_query($conn, "DELETE FROM tblclassteacher WHERE Id='$tid' LIMIT 1");

        if ($del) {
            $flash = "<div class='alert alert-success'>Teacher berhasil dihapus.</div>";
        } else {
            $flash = "<div class='alert alert-danger'>Gagal hapus: ".h(mysqli_error($conn))."</div>";
        }
    }
}

/* =========================
   LIST TEACHERS
========================= */
$res = mysqli_query($conn, "
    SELECT Id, firstName, lastName, emailAddress
    FROM tblclassteacher
    ORDER BY firstName ASC, lastName ASC
");
if (!$res) die('Query error: ' . mysqli_error($conn));

function getTeacherAssignments(mysqli $conn, int $teacherId): array {
    $teacherId = (int)$teacherId;
    $out = [];
    $q = mysqli_query($conn, "
        SELECT c.className, a.classArmName
        FROM tblteacher_class tc
        INNER JOIN tblclass c ON c.Id = tc.classId
        INNER JOIN tblclassarms a ON a.Id = tc.classArmId
        WHERE tc.teacherId = '$teacherId' AND tc.isActive = 1
        ORDER BY c.className ASC, a.classArmName ASC
    ");
    if ($q) while ($r = mysqli_fetch_assoc($q)) $out[] = trim($r['className'].' - '.$r['classArmName']);
    return $out;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Daftar Wali Kelas</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">

  <?php include 'Includes/sidebar.php'; ?>

  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
      <?php include 'Includes/topbar.php'; ?>

      <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Daftar Wali Kelas</h1>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Daftar Wali Kelas</li>
          </ol>
        </div>

        <?php echo $flash; ?>

        <div class="card mb-4">
          <div class="card-body">
            <div class="table-responsive p-3">
              <table class="table table-bordered table-hover" id="dataTable">
                <thead class="thead-light">
                  <tr>
                    <th style="width:60px;">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Kelas & Rombel yang dipegang</th>
                    <th style="width:320px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  while ($row = mysqli_fetch_assoc($res)):
                      $teacherId = (int)$row['Id'];
                      $fullName = trim($row['firstName'].' '.$row['lastName']);
                      $assignments = getTeacherAssignments($conn, $teacherId);
                  ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= h($fullName); ?></td>
                      <td><?= h($row['emailAddress']); ?></td>
                      <td>
                        <?php if (!$assignments): ?>
                          <span class="badge badge-secondary">Belum ada</span>
                        <?php else: ?>
                          <?php foreach ($assignments as $a): ?>
                            <span class="badge badge-info mr-1 mb-1"><?= h($a); ?></span>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </td>
                      <td class="text-nowrap">
                        <a class="btn btn-sm btn-primary"
                           href="teacherAssignments.php?teacherId=<?= $teacherId; ?>">
                          <i class="fas fa-layer-group"></i> Pegangan
                        </a>

                        <button class="btn btn-sm btn-warning"
                                data-toggle="modal"
                                data-target="#editTeacherModal"
                                data-id="<?= $teacherId; ?>"
                                data-first="<?= h($row['firstName']); ?>"
                                data-last="<?= h($row['lastName']); ?>"
                                data-email="<?= h($row['emailAddress']); ?>">
                          <i class="fas fa-edit"></i> Edit
                        </button>

                        <button class="btn btn-sm btn-danger"
                                data-toggle="modal"
                                data-target="#deleteTeacherModal"
                                data-id="<?= $teacherId; ?>"
                                data-name="<?= h($fullName); ?>">
                          <i class="fas fa-trash"></i> Hapus
                        </button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
    <?php include 'Includes/footer.php'; ?>
  </div>
</div>

<!-- =======================
     EDIT TEACHER MODAL
======================= -->
<div class="modal fade" id="editTeacherModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Teacher</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="teacherId" id="editTeacherId">

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Firstname</label>
            <input type="text" class="form-control" name="firstName" id="editFirstName" required>
          </div>
          <div class="form-group col-md-6">
            <label>Lastname</label>
            <input type="text" class="form-control" name="lastName" id="editLastName" required>
          </div>
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" class="form-control" name="emailAddress" id="editEmail" required>
        </div>

        <div class="form-group">
          <label>Password baru (opsional)</label>
          <input type="password" class="form-control" name="newPassword" placeholder="Kosongkan jika tidak ingin ubah password">
          <small class="text-muted">Jika diisi, password akan diubah (format hash mengikuti sistem saat ini).</small>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
        <button type="submit" name="updateTeacher" class="btn btn-warning">
          <i class="fas fa-save"></i> Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- =======================
     DELETE TEACHER MODAL
======================= -->
<div class="modal fade" id="deleteTeacherModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Hapus Teacher</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="teacherId" id="deleteTeacherId">
        <p class="mb-0">
          Anda yakin ingin menghapus teacher <strong id="deleteTeacherName"></strong>?
        </p>
        <small class="text-danger">
          Ini juga akan menghapus semua pegangan kelas teacher tersebut.
        </small>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
        <button type="submit" name="deleteTeacher" class="btn btn-danger">
          <i class="fas fa-trash"></i> Hapus
        </button>
      </div>
    </form>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="js/ruang-admin.min.js"></script>

<script>
  $(document).ready(function(){
    $('#dataTable').DataTable();

    // fill Edit modal
    $('#editTeacherModal').on('show.bs.modal', function (event) {
      var btn = $(event.relatedTarget);
      $('#editTeacherId').val(btn.data('id'));
      $('#editFirstName').val(btn.data('first'));
      $('#editLastName').val(btn.data('last'));
      $('#editEmail').val(btn.data('email'));
    });

    // fill Delete modal
    $('#deleteTeacherModal').on('show.bs.modal', function (event) {
      var btn = $(event.relatedTarget);
      $('#deleteTeacherId').val(btn.data('id'));
      $('#deleteTeacherName').text(btn.data('name'));
    });
  });
</script>
</body>
</html>
