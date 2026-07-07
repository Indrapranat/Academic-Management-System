<?php
// Admin/createClassTeacher.php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include '../Includes/dbcon.php';
include '../Includes/session.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$error = '';
$success = '';

// ============================
// PROSES SIMPAN TEACHER + ASSIGNMENTS
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveTeacher'])) {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $email     = trim($_POST['emailAddress'] ?? '');
    $phone     = trim($_POST['phoneNo'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    // assignments[] berisi string "classId|armId"
    $assignments = $_POST['assignments'] ?? [];
    if (!is_array($assignments)) $assignments = [];

    if ($firstName === '' || $lastName === '' || $email === '' || $phone === '' || $password === '') {
        $error = "Semua field bertanda * wajib diisi.";
    } elseif (count($assignments) === 0) {
        $error = "Pilih minimal 1 pegangan kelas (misalnya 7 - A).";
    } else {
        $emailEsc = mysqli_real_escape_string($conn, $email);

        // Cek duplikat email
        $cekEmail = mysqli_query($conn, "SELECT Id FROM tblclassteacher WHERE emailAddress='$emailEsc' LIMIT 1");
        if ($cekEmail && mysqli_num_rows($cekEmail) > 0) {
            $error = "Email sudah terdaftar sebagai wali kelas lain.";
        } else {

            // Validasi format assignment dan pastikan arm memang milik class tsb
            $pairs = [];
            foreach ($assignments as $key) {
                $key = trim((string)$key);
                if ($key === '' || strpos($key, '|') === false) continue;

                [$classId, $armId] = explode('|', $key, 2);
                $classId = (int)$classId;
                $armId   = (int)$armId;

                if ($classId <= 0 || $armId <= 0) continue;

                $chk = mysqli_query($conn, "
                    SELECT Id FROM tblclassarms
                    WHERE Id='$armId' AND classId='$classId'
                    LIMIT 1
                ");
                if ($chk && mysqli_num_rows($chk) === 1) {
                    $pairs[] = [$classId, $armId];
                }
            }

            // Hilangkan duplikat
            $uniq = [];
            foreach ($pairs as [$c,$a]) $uniq[$c.'|'.$a] = [$c,$a];
            $pairs = array_values($uniq);

            if (count($pairs) === 0) {
                $error = "Pegangan kelas tidak valid (cek data kelas/rombel).";
            } else {
                // Insert teacher
                $firstEsc = mysqli_real_escape_string($conn, $firstName);
                $lastEsc  = mysqli_real_escape_string($conn, $lastName);
                $phoneEsc = mysqli_real_escape_string($conn, $phone);

                // Simpan MD5 agar konsisten dengan login teacher kamu
                $passHash = md5($password);
                $passEsc  = mysqli_real_escape_string($conn, $passHash);

                // OPTIONAL: isi legacy classId/classArmId biar tidak null (ambil assignment pertama)
                $legacyClassId = (string)$pairs[0][0];
                $legacyArmId   = (string)$pairs[0][1];

                $ins = mysqli_query($conn, "
                    INSERT INTO tblclassteacher
                        (firstName, lastName, emailAddress, phoneNo, classId, classArmId, password, dateCreated)
                    VALUES
                        ('$firstEsc', '$lastEsc', '$emailEsc', '$phoneEsc',
                         '$legacyClassId', '$legacyArmId',
                         '$passEsc', NOW())
                ");

                if (!$ins) {
                    $error = "Gagal menyimpan teacher: ".mysqli_error($conn);
                } else {
                    $teacherId = (int)mysqli_insert_id($conn);

                    // Insert pivot assignments
                    $ok = true;
                    foreach ($pairs as [$c,$a]) {
                        $cEsc = mysqli_real_escape_string($conn, (string)$c);
                        $aEsc = mysqli_real_escape_string($conn, (string)$a);

                        $p = mysqli_query($conn, "
                            INSERT IGNORE INTO tblteacher_class (teacherId, classId, classArmId, isActive)
                            VALUES ('$teacherId', '$cEsc', '$aEsc', 1)
                        ");
                        if (!$p) $ok = false;
                    }

                    if ($ok) {
                        $success = "Teacher berhasil disimpan + pegangan kelas berhasil ditambahkan.";
                        // reset form values
                        $firstName = $lastName = $email = $phone = $password = '';
                        $assignments = [];
                    } else {
                        $success = "Teacher tersimpan, tapi ada pegangan yang gagal tersimpan. Cek DB.";
                    }
                }
            }
        }
    }
}

// ============================
// DATA UNTUK PILIH PEGANGAN (7 - A, dst)
// ============================
$armRes = mysqli_query($conn, "
    SELECT a.Id AS armId, a.classArmName, a.classId, c.className
    FROM tblclassarms a
    INNER JOIN tblclass c ON c.Id = a.classId
    ORDER BY c.className ASC, a.classArmName ASC
");
$armOptions = [];
if ($armRes) {
    while ($r = mysqli_fetch_assoc($armRes)) {
        $key = (int)$r['classId'].'|'.(int)$r['armId'];
        $armOptions[] = [
            'key' => $key,
            'label' => trim($r['className'].' - '.$r['classArmName'])
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Tambah Wali Kelas</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
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
          <h1 class="h3 mb-0 text-gray-800">Tambah Wali Kelas</h1>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Wali Kelas</li>
          </ol>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= h($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success"><?= h($success); ?></div>
        <?php endif; ?>

        <div class="row">
          <div class="col-lg-12">
            <div class="card mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Form Wali Kelas</h6>
                <a href="viewClassTeachers.php" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-list"></i> Lihat Daftar
                </a>
              </div>

              <div class="card-body">
                <form method="post">
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Nama Depan *</label>
                      <input type="text" name="firstName" class="form-control" required value="<?= h($firstName ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Nama Belakang *</label>
                      <input type="text" name="lastName" class="form-control" required value="<?= h($lastName ?? ''); ?>">
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Email *</label>
                      <input type="email" name="emailAddress" class="form-control" required value="<?= h($email ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-3">
                      <label>No. HP *</label>
                      <input type="text" name="phoneNo" class="form-control" required value="<?= h($phone ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-3">
                      <label>Password Login *</label>
                      <input type="text" name="password" class="form-control" required value="<?= h($password ?? 'pass123'); ?>">
                      <small class="text-muted">Disimpan dalam format hash yang dipakai sistem login.</small>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Pegangan Kelas (boleh lebih dari 1) *</label>

                    <div class="d-flex mb-2" style="gap:8px; flex-wrap:wrap;">
                      <input id="filterPegangan" type="text" class="form-control" style="max-width:320px;"
                             placeholder="Cari: 7 - A, 8 - B, dst">
                      <button type="button" class="btn btn-light btn-sm" id="btnSelectAll">
                        Pilih Semua
                      </button>
                      <button type="button" class="btn btn-light btn-sm" id="btnClearAll">
                        Kosongkan
                      </button>
                    </div>

                    <div class="border rounded p-3" style="max-height:260px; overflow:auto;" id="peganganBox">
                      <?php if (count($armOptions) === 0): ?>
                        <div class="text-danger">Data class/rombel kosong. Isi tblclass dan tblclassarms dulu.</div>
                      <?php else: ?>
                        <div class="row">
                          <?php foreach ($armOptions as $opt): ?>
                            <?php $checked = in_array($opt['key'], $assignments ?? [], true); ?>
                            <div class="col-md-3 mb-2 peg-item" data-label="<?= h(strtolower($opt['label'])); ?>">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input peg-check"
                                       id="peg<?= h($opt['key']); ?>"
                                       name="assignments[]"
                                       value="<?= h($opt['key']); ?>"
                                       <?= $checked ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="peg<?= h($opt['key']); ?>">
                                  <?= h($opt['label']); ?>
                                </label>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      <?php endif; ?>
                    </div>

                    <small class="text-muted">
                      Pilih kombinasi <b>Kelas - Rombel</b>, contoh: <b>7 - A</b>, <b>8 - C</b>.
                    </small>
                  </div>

                  <button type="submit" name="saveTeacher" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                  </button>
                </form>
              </div>
            </div>

            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Catatan</h6>
              </div>
              <div class="card-body">
                Pegangan kelas disimpan di tabel <b>tblteacher_class</b>.
                Halaman daftar & edit/hapus teacher ada di <b>viewClassTeachers.php</b>.
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
    <?php include 'Includes/footer.php'; ?>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/ruang-admin.min.js"></script>

<script>
(function(){
  var input = document.getElementById('filterPegangan');
  var box = document.getElementById('peganganBox');

  if (input) {
    input.addEventListener('input', function(){
      var q = (input.value || '').toLowerCase();
      var items = box.querySelectorAll('.peg-item');
      items.forEach(function(it){
        var label = it.getAttribute('data-label') || '';
        it.style.display = label.indexOf(q) !== -1 ? '' : 'none';
      });
    });
  }

  var btnAll = document.getElementById('btnSelectAll');
  var btnClr = document.getElementById('btnClearAll');

  if (btnAll) btnAll.addEventListener('click', function(){
    box.querySelectorAll('.peg-check').forEach(function(ch){ ch.checked = true; });
  });

  if (btnClr) btnClr.addEventListener('click', function(){
    box.querySelectorAll('.peg-check').forEach(function(ch){ ch.checked = false; });
  });
})();
</script>
</body>
</html>
