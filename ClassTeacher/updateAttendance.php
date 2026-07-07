<?php
include '../Includes/dbcon.php';
include 'Includes/session.php';

// Validasi basic
if (
    !isset($_POST['meetingId'], $_POST['admissionNo'], $_POST['status'])
) {
    die("Invalid request.");
}

$meetingId   = intval($_POST['meetingId']);
$admissionNo = mysqli_real_escape_string($conn, $_POST['admissionNo']);
$status      = intval($_POST['status']); // 0 = Alfa, 1 = Hadir, 2 = Izin

if ($meetingId <= 0 || $admissionNo === '') {
    die("Invalid data.");
}

// Cek apakah sudah ada record attendance utk siswa+meeting ini
$cek = mysqli_query($conn, "
    SELECT Id 
    FROM tblattendance
    WHERE meetingId   = '$meetingId'
      AND admissionNo = '$admissionNo'
    LIMIT 1
");

if ($cek && mysqli_num_rows($cek) > 0) {
    // UPDATE status saja
    $row = mysqli_fetch_assoc($cek);
    $attId = $row['Id'];

    mysqli_query($conn, "
        UPDATE tblattendance
        SET status = '$status'
        WHERE Id = '$attId'
        LIMIT 1
    ");
} else {
    // Belum ada record → INSERT baru
    // Ambil classId & classArmId siswa
    $sRes = mysqli_query($conn, "
        SELECT classId, classArmId
        FROM tblstudents
        WHERE admissionNumber = '$admissionNo'
        LIMIT 1
    ");
    $s = $sRes ? mysqli_fetch_assoc($sRes) : null;

    if (!$s) {
        die("Student not found.");
    }

    $classId    = (int)$s['classId'];
    $classArmId = (int)$s['classArmId'];

    // Ambil sessionTerm aktif
    $sessionRes = mysqli_query($conn, "
        SELECT Id FROM tblsessionterm
        WHERE isActive = 1
        LIMIT 1
    ");
    $sessionRow = $sessionRes ? mysqli_fetch_assoc($sessionRes) : null;
    $sessionTermId = $sessionRow ? (int)$sessionRow['Id'] : null;

    if (!$sessionTermId) {
        die("Active Session/Term not set.");
    }

    $now = date('Y-m-d H:i:s');

    mysqli_query($conn, "
        INSERT INTO tblattendance
          (admissionNo, classId, classArmId, sessionTermId, meetingId, status, dateTimeTaken)
        VALUES
          ('$admissionNo', '$classId', '$classArmId', '$sessionTermId', '$meetingId', '$status', '$now')
    ");
}

// Kembali ke halaman view meeting
header("Location: viewMeeting.php?id=" . $meetingId);
exit;
