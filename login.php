<?php
include 'Includes/dbcon.php';
require_once __DIR__ . '/Includes/session_bootstrap.php';

function redirectTo(string $path): void {
    session_write_close();
    header("Location: $path");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $userType = $_POST['userType'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($userType === 'Administrator') {

        bootRoleSession('admin');

        $passwordHash = md5($password);
        $usernameEsc = mysqli_real_escape_string($conn, $username);
        $q = "SELECT * FROM tbladmin WHERE emailAddress='$usernameEsc' AND password='$passwordHash'";
        $rs = $conn->query($q);

        if ($rs && $rs->num_rows === 1) {
            $row = $rs->fetch_assoc();
            $_SESSION['role']         = 'admin';
            $_SESSION['userId']       = $row['Id'];
            $_SESSION['firstName']    = $row['firstName'];
            $_SESSION['lastName']     = $row['lastName'];
            $_SESSION['emailAddress'] = $row['emailAddress'];

            redirectTo("Admin/index.php");
        } else {
            $error = "Invalid Username/Password!";
        }

    } elseif ($userType === 'ClassTeacher') {

        bootRoleSession('teacher');

        $passwordHash = md5($password);
        $usernameEsc = mysqli_real_escape_string($conn, $username);
        $q = "SELECT * FROM tblclassteacher WHERE emailAddress='$usernameEsc' AND password='$passwordHash'";
        $rs = $conn->query($q);

        if ($rs && $rs->num_rows === 1) {
            $row = $rs->fetch_assoc();

            // ====== SESSION TEACHER (MANY-TO-MANY) ======
            $_SESSION['role']         = 'teacher';
            $_SESSION['userId']       = $row['Id'];
            $_SESSION['firstName']    = $row['firstName'];
            $_SESSION['lastName']     = $row['lastName'];
            $_SESSION['emailAddress'] = $row['emailAddress'];

            // HAPUS penggunaan classId/classArmId tunggal (biar tidak nge-lock 1 kelas)
            unset($_SESSION['classId'], $_SESSION['classArmId']);

            // Opsional: pastikan teacher punya assignment di tblteacher_class
            $teacherId = (int)$row['Id'];
            $chk = mysqli_query($conn, "
                SELECT id FROM tblteacher_class
                WHERE teacherId='$teacherId' AND isActive=1
                LIMIT 1
            ");
            if (!$chk || mysqli_num_rows($chk) === 0) {
                // kalau belum ada assignment, jangan masuk dashboard (biar tidak bingung)
                $error = "Teacher belum punya kelas. Hubungi admin untuk set assignment.";
            } else {
                redirectTo("ClassTeacher/index.php");
            }

        } else {
            $error = "Invalid Username/Password!";
        }

    } elseif ($userType === 'Student') {

        bootRoleSession('student');

        $usernameEsc = mysqli_real_escape_string($conn, $username);
        $passwordEsc = mysqli_real_escape_string($conn, $password);
        $q = "SELECT * FROM tblstudents WHERE admissionNumber='$usernameEsc' AND password='$passwordEsc'";
        $rs = $conn->query($q);

        if ($rs && $rs->num_rows === 1) {
            $row = $rs->fetch_assoc();
            $_SESSION['role']            = 'student';
            $_SESSION['studentId']       = $row['Id'];
            $_SESSION['admissionNumber'] = $row['admissionNumber'];
            $_SESSION['classId']         = $row['classId'];
            $_SESSION['classArmId']      = $row['classArmId'];

            redirectTo("student/index.php");
        } else {
            $error = "Invalid Admission No/Password!";
        }

    } else {
        $error = "Please select a valid user role.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/attnlg.png" rel="icon">
  <title>AMS - Login</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-login" style="background-image: url('img/logo/loral1.jpeg');">
<div class="container-login">
  <div class="row justify-content-center">
    <div class="col-xl-10 col-lg-12 col-md-9">
      <div class="card shadow-sm my-5">
        <div class="card-body p-0">
          <div class="row">
            <div class="col-lg-12">
              <div class="login-form">
                <h5 class="text-center">STUDENT ATTENDANCE SYSTEM</h5>
                <div class="text-center">
                  <img src="img/logo/attnlg.png" style="width:100px;height:100px">
                  <br><br>
                  <h1 class="h4 text-gray-900 mb-4">Login Panel</h1>
                </div>

                <?php if ($error): ?>
                  <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form class="user" method="post" action="">
                  <div class="form-group">
                    <select required name="userType" class="form-control mb-3">
                      <option value="">--Select User Roles--</option>
                      <option value="Administrator">Administrator</option>
                      <option value="ClassTeacher">ClassTeacher</option>
                      <option value="Student">Student</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <input type="text" class="form-control" required name="username"
                      placeholder="Email (Admin/Teacher) atau Admission No (Student)">
                  </div>

                  <div class="form-group">
                    <input type="password" name="password" required class="form-control"
                      placeholder="Enter Password">
                  </div>

                  <div class="form-group">
                    <input type="submit" class="btn btn-success btn-block" value="Login" name="login">
                  </div>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
</body>
</html>
