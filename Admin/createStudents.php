<?php
// Admin/createStudents.php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/Includes/session.php';   // session admin (bootRoleSession('admin'))
require_once __DIR__ . '/../Includes/dbcon.php';  // DB

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$statusMsg = '';
$row = [
  'fullName' => '',
  'admissionNumber' => '',
  'classId' => '',
  'classArmId' => ''
];

function classArmDropdownScript() {
  ?>
  <script>
    function classArmDropdown(str) {
      if (str == "") { document.getElementById("txtHint").innerHTML = ""; return; }
      var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
      xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          document.getElementById("txtHint").innerHTML = this.responseText;
        }
      };
      xmlhttp.open("GET","ajaxClassArms2.php?cid="+encodeURIComponent(str),true);
      xmlhttp.send();
    }
  </script>
  <?php
}

// ------------------------ SAVE ------------------------
if (isset($_POST['save'])) {

  $firstName = trim($_POST['firstName'] ?? '');
  $lastName  = trim($_POST['lastName'] ?? '');
  $otherName = trim($_POST['otherName'] ?? '');

  $fullName = trim($firstName.' '.$lastName.' '.$otherName);

  $admissionNumber = trim($_POST['admissionNumber'] ?? '');
  $classId    = trim($_POST['classId'] ?? '');
  $classArmId = trim($_POST['classArmId'] ?? '');
  $dateCreated = date("Y-m-d");

  if ($fullName === '' || $admissionNumber === '' || $classId === '' || $classArmId === '') {
    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Semua field wajib diisi (Class Arm harus dipilih).</div>";
  } else {

    $fullNameEsc = mysqli_real_escape_string($conn, $fullName);
    $admEsc      = mysqli_real_escape_string($conn, $admissionNumber);

    $check = mysqli_query($conn, "SELECT Id FROM tblstudents WHERE admissionNumber='$admEsc' LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Admission Number sudah ada.</div>";
    } else {

      $ins = mysqli_query($conn, "
        INSERT INTO tblstudents
          (fullName, admissionNumber, password, classId, classArmId, dateCreated)
        VALUES
          ('$fullNameEsc', '$admEsc', '12345', '$classId', '$classArmId', '$dateCreated')
      ");

      if ($ins) {
        $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Created Successfully!</div>";
      } else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>DB Error: ".h(mysqli_error($conn))."</div>";
      }
    }
  }
}

// ------------------------ EDIT ------------------------
$Id = null;
if (isset($_GET['Id'], $_GET['action']) && $_GET['action'] === "edit") {
  $Id = (int)$_GET['Id'];

  $q = mysqli_query($conn, "SELECT * FROM tblstudents WHERE Id='$Id' LIMIT 1");
  if ($q && mysqli_num_rows($q) === 1) {
    $row = mysqli_fetch_assoc($q);
  }

  if (isset($_POST['update'])) {

    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $otherName = trim($_POST['otherName'] ?? '');
    $fullName  = trim($firstName.' '.$lastName.' '.$otherName);

    $admissionNumber = trim($_POST['admissionNumber'] ?? '');
    $classId    = trim($_POST['classId'] ?? '');
    $classArmId = trim($_POST['classArmId'] ?? '');

    if ($fullName === '' || $admissionNumber === '' || $classId === '' || $classArmId === '') {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Semua field wajib diisi (Class Arm harus dipilih).</div>";
    } else {

      $fullNameEsc = mysqli_real_escape_string($conn, $fullName);
      $admEsc      = mysqli_real_escape_string($conn, $admissionNumber);

      $upd = mysqli_query($conn, "
        UPDATE tblstudents SET
          fullName='$fullNameEsc',
          admissionNumber='$admEsc',
          password='12345',
          classId='$classId',
          classArmId='$classArmId'
        WHERE Id='$Id'
      ");

      if ($upd) {
        header("Location: createStudents.php");
        exit;
      } else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>DB Error: ".h(mysqli_error($conn))."</div>";
      }
    }
  }
}

// ------------------------ DELETE ------------------------
if (isset($_GET['Id'], $_GET['action']) && $_GET['action'] === "delete") {
  $Id = (int)$_GET['Id'];
  $del = mysqli_query($conn, "DELETE FROM tblstudents WHERE Id='$Id'");
  if ($del) {
    header("Location: createStudents.php");
    exit;
  } else {
    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>DB Error: ".h(mysqli_error($conn))."</div>";
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <?php include 'includes/title.php';?>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <?php classArmDropdownScript(); ?>
</head>

<body id="page-top">
<div id="wrapper">

  <?php include "Includes/sidebar.php";?>

  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

      <?php include "Includes/topbar.php";?>

      <div class="container-fluid" id="container-wrapper">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Create Students</h1>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Students</li>
          </ol>
        </div>

        <div class="row">
          <div class="col-lg-12">

            <div class="card mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Create Students</h6>
                <?php echo $statusMsg; ?>
              </div>

              <div class="card-body">
                <form method="post">

                  <div class="form-group row mb-3">
                    <div class="col-xl-4">
                      <label class="form-control-label">Firstname<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" name="firstName"
                             value="<?php
                               if (!empty($row['fullName'])) {
                                 $parts = preg_split('/\s+/', trim($row['fullName']));
                                 echo h($parts[0] ?? '');
                               }
                             ?>">
                    </div>

                    <div class="col-xl-4">
                      <label class="form-control-label">Lastname<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" name="lastName"
                             value="<?php
                               if (!empty($row['fullName'])) {
                                 $parts = preg_split('/\s+/', trim($row['fullName']));
                                 echo h($parts[1] ?? '');
                               }
                             ?>">
                    </div>

                    <div class="col-xl-4">
                      <label class="form-control-label">Other Name</label>
                      <input type="text" class="form-control" name="otherName"
                             value="<?php
                               if (!empty($row['fullName'])) {
                                 $parts = preg_split('/\s+/', trim($row['fullName']));
                                 $rest = '';
                                 if (count($parts) > 2) $rest = implode(' ', array_slice($parts, 2));
                                 echo h($rest);
                               }
                             ?>">
                    </div>
                  </div>

                  <div class="form-group row mb-3">
                    <div class="col-xl-6">
                      <label class="form-control-label">Admission Number<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" required name="admissionNumber"
                             value="<?php echo h($row['admissionNumber'] ?? ''); ?>">
                    </div>

                    <div class="col-xl-6">
                      <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
                      <?php
                        $qry= "SELECT * FROM tblclass ORDER BY className ASC";
                        $result = $conn->query($qry);
                        if ($result && $result->num_rows > 0){
                          echo '<select required name="classId" onchange="classArmDropdown(this.value)" class="form-control mb-3">';
                          echo '<option value="">--Select Class--</option>';
                          while ($c = $result->fetch_assoc()){
                            $selected = (!empty($row['classId']) && (string)$row['classId'] === (string)$c['Id']) ? 'selected' : '';
                            echo '<option value="'.h($c['Id']).'" '.$selected.'>'.h($c['className']).'</option>';
                          }
                          echo '</select>';
                        } else {
                          echo "<div class='text-danger'>Tidak ada data class.</div>";
                        }
                      ?>
                    </div>
                  </div>

                  <div class="form-group row mb-3">
                    <div class="col-xl-6">
                      <label class="form-control-label">Class Arm<span class="text-danger ml-2">*</span></label>
                      <div id="txtHint"></div>
                      <small class="text-muted">Pilih Class dulu agar Class Arm muncul.</small>
                    </div>
                  </div>

                  <?php if ($Id): ?>
                    <button type="submit" name="update" class="btn btn-warning">Update</button>
                  <?php else: ?>
                    <button type="submit" name="save" class="btn btn-primary">Save</button>
                  <?php endif; ?>

                </form>
              </div>
            </div>

            <div class="card mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">All Student</h6>
              </div>

              <div class="table-responsive p-3">
                <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                  <thead class="thead-light">
                    <tr>
                      <th>#</th>
                      <th>Full Name</th>
                      <th>Admission No</th>
                      <th>Class</th>
                      <th>Class Arm</th>
                      <th>Date Created</th>
                      <th>Edit</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $query = "
                        SELECT 
                          s.Id, s.fullName, s.admissionNumber, s.dateCreated,
                          c.className, a.classArmName
                        FROM tblstudents s
                        INNER JOIN tblclass c ON c.Id = s.classId
                        INNER JOIN tblclassarms a ON a.Id = s.classArmId
                        ORDER BY s.Id DESC
                      ";
                      $rs = $conn->query($query);
                      $sn = 0;
                      if ($rs && $rs->num_rows > 0) {
                        while ($r = $rs->fetch_assoc()) {
                          $sn++;
                          echo "
                            <tr>
                              <td>".$sn."</td>
                              <td>".h($r['fullName'])."</td>
                              <td>".h($r['admissionNumber'])."</td>
                              <td>".h($r['className'])."</td>
                              <td>".h($r['classArmName'])."</td>
                              <td>".h($r['dateCreated'])."</td>
                              <td><a href='?action=edit&Id=".((int)$r['Id'])."'><i class='fas fa-fw fa-edit'></i></a></td>
                              <td><a href='?action=delete&Id=".((int)$r['Id'])."' onclick='return confirm(\"Delete this student?\");'><i class='fas fa-fw fa-trash'></i></a></td>
                            </tr>
                          ";
                        }
                      } else {
                        echo "<tr><td colspan='8' class='text-center'>No Record Found!</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

    <?php include "Includes/footer.php";?>
  </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
  <i class="fas fa-angle-up"></i>
</a>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
  $(document).ready(function () {
    $('#dataTableHover').DataTable();
  });
</script>
</body>
</html>
