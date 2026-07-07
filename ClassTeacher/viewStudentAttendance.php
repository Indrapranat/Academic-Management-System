<?php 

error_reporting(0);
require_once __DIR__ . '/Includes/session.php';   // ✅ teacher guard (bootRoleSession teacher)
require_once __DIR__ . '/../Includes/dbcon.php';  // ✅ DB
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>View Student Attendance</title>

  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

  <!-- DATATABLES -->
  <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

  <script>
    function typeDropDown(str) {
      if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
      }

      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          document.getElementById("txtHint").innerHTML = this.responseText;
        }
      };
      xmlhttp.open("GET", "ajaxCallTypes.php?tid=" + str, true);
      xmlhttp.send();
    }
  </script>

</head>

<body id="page-top">

<div id="wrapper">

  <!-- Sidebar -->
  <?php include "Includes/sidebar.php"; ?>
  <!-- End Sidebar -->


  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

      <!-- Topbar -->
      <?php include "Includes/topbar.php"; ?>
      <!-- End Topbar -->

      <div class="container-fluid" id="container-wrapper">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">View Attendance</h1>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./">Home</a></li>
            <li class="breadcrumb-item active">View Attendance</li>
          </ol>
        </div>

        <!-- FORM FILTER -->
        <div class="card mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Attendance</h6>
          </div>

          <div class="card-body">
            <form method="POST">

              <div class="form-group row mb-4">

                <!-- STUDENT SELECT -->
                <div class="col-xl-6">
                  <label>Select Student <span class="text-danger">*</span></label>
                  <?php
                    $qry = "
                      SELECT Id, fullName, admissionNumber 
                      FROM tblstudents 
                      WHERE classId = '$_SESSION[classId]' 
                        AND classArmId = '$_SESSION[classArmId]'
                      ORDER BY fullName ASC
                    ";
                    $result = $conn->query($qry);

                    echo '<select required name="admissionNo" class="form-control">';
                    echo '<option value="">--Select Student--</option>';

                    while ($row = $result->fetch_assoc()) {
                      echo "<option value='{$row['admissionNumber']}'>
                              {$row['fullName']} ({$row['admissionNumber']})
                            </option>";
                    }
                    echo '</select>';
                  ?>
                </div>

                <!-- TYPE SELECT -->
                <div class="col-xl-6">
                  <label>Type <span class="text-danger">*</span></label>
                  <select required name="type" onchange="typeDropDown(this.value)" class="form-control">
                    <option value="">--Select--</option>
                    <option value="1">All</option>
                    <option value="2">Single Date</option>
                    <option value="3">Date Range</option>
                  </select>
                </div>

              </div>

              <!-- AJAX LOAD RANGE / SINGLE DATE INPUT -->
              <div id="txtHint"></div>

              <button type="submit" name="view" class="btn btn-primary mt-3">
                View Attendance
              </button>

            </form>
          </div>
        </div>

        <!-- RESULT TABLE -->
        <div class="card mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Attendance Records</h6>
          </div>

          <div class="table-responsive p-3">
            <table class="table align-items-center table-flush table-hover" id="dataTableHover">
              <thead class="thead-light">
                <tr>
                  <th>#</th>
                  <th>Nama</th>
                  <th>NIM</th>
                  <th>Kelas</th>
                  <th>Pertemuan</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                </tr>
              </thead>
              <tbody>

              <?php
              if (isset($_POST['view'])) {

                $admissionNo = $_POST['admissionNo'];
                $type        = $_POST['type'];

                // ================================
                //  QUERY BUILDER
                // ================================
                $dateFilter = "";

                if ($type == "2") {
                  $single = $_POST['singleDate'];
                  $dateFilter = " AND DATE(t.dateTimeTaken) = '$single' ";
                } 
                elseif ($type == "3") {
                  $from = $_POST['fromDate'];
                  $to   = $_POST['toDate'];
                  $dateFilter = " AND DATE(t.dateTimeTaken) BETWEEN '$from' AND '$to' ";
                }

                // ================================
                //  MAIN QUERY (CLEAN VERSION)
                // ================================
                $query = "
                  SELECT
                      s.fullName,
                      s.admissionNumber,
                      c.className,
                      a.classArmName,
                      t.meetingNo,
                      t.status,
                      t.dateTimeTaken
                  FROM tblattendance t
                  JOIN tblstudents s ON s.admissionNumber = t.admissionNo
                  JOIN tblclass c ON c.Id = t.classId
                  JOIN tblclassarms a ON a.Id = t.classArmId
                  WHERE t.admissionNo = '$admissionNo'
                    AND t.classId = '$_SESSION[classId]'
                    AND t.classArmId = '$_SESSION[classArmId]'
                    $dateFilter
                  ORDER BY t.dateTimeTaken DESC
                ";

                $rs = $conn->query($query);
                $sn = 0;

                if ($rs->num_rows > 0) {
                  while ($row = $rs->fetch_assoc()) {

                    $sn++;

                    $status = $row['status'] == 1 ? 
                      "<span class='badge badge-success'>Hadir</span>" :
                      "<span class='badge badge-danger'>Alfa</span>";

                    echo "
                      <tr>
                        <td>$sn</td>
                        <td>{$row['fullName']}</td>
                        <td>{$row['admissionNumber']}</td>
                        <td>{$row['className']} - {$row['classArmName']}</td>
                        <td>{$row['meetingNo']}</td>
                        <td>$status</td>
                        <td>{$row['dateTimeTaken']}</td>
                      </tr>
                    ";
                  }
                } 
                else {
                  echo "
                    <tr>
                      <td colspan='7' class='text-center text-danger'>
                        No attendance records found.
                      </td>
                    </tr>
                  ";
                }
              }
              ?>

              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

    <!-- Footer -->
    <?php include "Includes/footer.php"; ?>
    <!-- End Footer -->

  </div>
</div>


<!-- Scripts -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>

<!-- DATATABLES -->
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
  $(document).ready(function () {
    $('#dataTableHover').DataTable({
      dom: 'Bfrtip',
      buttons: ['excel', 'pageLength']
    });
  });
</script>

</body>
</html>
