<?php
session_start();
include 'Includes/dbcon.php';

if (isset($_POST['login'])) {
    $admissionNumber = mysqli_real_escape_string($conn, $_POST['admissionNumber']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM tblstudents 
            WHERE admissionNumber = '$admissionNumber' 
            AND password = '$password'"; // nanti bisa diganti hash
    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) == 1) {
        $row = mysqli_fetch_assoc($query);

        $_SESSION['role']          = 'student';
        $_SESSION['studentId']     = $row['Id'];
        $_SESSION['admissionNumber'] = $row['admissionNumber'];
        $_SESSION['classId']       = $row['classId'];
        $_SESSION['classArmId']    = $row['classArmId'];

        header('Location: student/index.php');
        exit;
    } else {
        $error = "Admission No atau password salah";
    }
}
?>
<!-- Form HTML very simple -->
<form method="post">
    <input type="text" name="admissionNumber" placeholder="Admission Number" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
</form>
<?php if(isset($error)) echo "<p>$error</p>"; ?>
