<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$teacherId  = (int)($_SESSION['userId'] ?? 0);
$classId    = (int)($_SESSION['classId'] ?? 0);
$classArmId = (int)($_SESSION['classArmId'] ?? 0);

$studentId = (int)($_GET['id'] ?? 0);

if ($teacherId <= 0 || $studentId <= 0 || $classId <= 0 || $classArmId <= 0) {
    die("Invalid request.");
}

// Ambil data siswa, pastikan di kelas teacher
$sRes = mysqli_query($conn, "
    SELECT admissionNumber
    FROM tblstudents
    WHERE Id = '$studentId'
      AND classId = '$classId'
      AND classArmId = '$classArmId'
    LIMIT 1
");
$s = $sRes ? mysqli_fetch_assoc($sRes) : null;

if (!$s) {
    die("Siswa tidak ditemukan di kelas Anda.");
}

$adm = mysqli_real_escape_string($conn, $s['admissionNumber']);

// Hapus semua attendance siswa ini
mysqli_query($conn, "
    DELETE FROM tblattendance
    WHERE admissionNo = '$adm'
");

// Hapus siswa
mysqli_query($conn, "
    DELETE FROM tblstudents
    WHERE Id = '$studentId'
    LIMIT 1
");

header("Location: viewStudents.php");
exit;
