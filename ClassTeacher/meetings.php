<?php
include '../Includes/dbcon.php';
include 'Includes/session.php';

$teacherId  = $_SESSION['userId'];
$classId    = $_SESSION['classId'];
$classArmId = $_SESSION['classArmId'];

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = "<div class='alert alert-info'>".$_SESSION['msg']."</div>";
    unset($_SESSION['msg']);
}

// DELETE meeting
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM tblmeetings WHERE Id='$id' LIMIT 1");
    $_SESSION['msg'] = "Meeting berhasil dihapus.";
    header("Location: meetings.php");
    exit;
}

// Ambil list meeting
$meetings = mysqli_query($conn, "
    SELECT *
    FROM tblmeetings
    WHERE classId='$classId' AND classArmId='$classArmId'
    ORDER BY meetingNo ASC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Meetings</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

  <?php include "Includes/sidebar.php"; ?>

  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

      <?php include "Includes/topbar.php"; ?>

      <div class="container-fluid">

        <div class="d-flex justify-content-between mb-3">
            <h3 class="text-gray-800">Daftar Pertemuan</h3>
            <a href="newMeeting.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Meeting</a>
        </div>

        <?php echo $msg; ?>

        <div class="card mb-4">
          <div class="card-body">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Pertemuan</th>
                  <th>Tanggal</th>
                  <th>Jam</th>
                  <th>Aksi</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $i = 1;
                while ($m = mysqli_fetch_assoc($meetings)):
                ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td>Pertemuan <?php echo $m['meetingNo']; ?></td>
                  <td><?php echo $m['meetingDate']; ?></td>
                  <td><?php echo $m['meetingTime']; ?></td>
                  <td>
                    <a href="viewMeeting.php?id=<?php echo $m['Id']; ?>" class="btn btn-info btn-sm">
                      <i class="fas fa-eye"></i> View
                    </a>

                    <a onclick="return confirm('Delete?')" 
                       href="meetings.php?delete=<?php echo $m['Id']; ?>" 
                       class="btn btn-danger btn-sm">
                      <i class="fas fa-trash"></i>
                    </a>
                  </td>
                </tr>
                <?php endwhile; ?>

              </tbody>
            </table>
          </div>
        </div>

      </div>

    </div>
    <?php include "Includes/footer.php"; ?>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
