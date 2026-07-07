<?php
include '../Includes/dbcon.php';
include 'includes/session.php';

$studentId   = $_SESSION['studentId'];
$admissionNo = $_SESSION['admissionNumber'];
$classId     = $_SESSION['classId'];
$classArmId  = $_SESSION['classArmId'];

if (!isset($_POST['present'], $_POST['meetingId'])) {
    $_SESSION['attendance_msg'] = "Permintaan absensi tidak valid.";
    header("Location: index.php");
    exit;
}

$meetingId = (int)$_POST['meetingId'];
$todayDate = date('Y-m-d');
$nowTime   = date('H:i:s');

// ------------------ CEK MEETINGNYA ------------------
$meetingRes = mysqli_query($conn, "
    SELECT Id, meetingNo, meetingDate, meetingTime, meetingEndTime
    FROM tblmeetings
    WHERE Id = '$meetingId'
      AND classId    = '$classId'
      AND classArmId = '$classArmId'
    LIMIT 1
");

if (!$meetingRes || mysqli_num_rows($meetingRes) === 0) {
    $_SESSION['attendance_msg'] = "Meeting tidak ditemukan untuk kelas Anda.";
    header("Location: index.php");
    exit;
}

$meeting = mysqli_fetch_assoc($meetingRes);

// OPTIONAL: kalau mau paksa hanya bisa absen di tanggal yg sama
// if ($meeting['meetingDate'] != $todayDate) {
//     $_SESSION['attendance_msg'] = "Absensi hanya dapat dilakukan pada tanggal meeting.";
//     header("Location: index.php");
//     exit;
// }

// ------------------ CEK SUDAH ABSEN BELUM ------------------
$cekRes = mysqli_query($conn, "
    SELECT Id FROM tblattendance
    WHERE admissionNo = '$admissionNo'
      AND classId     = '$classId'
      AND classArmId  = '$classArmId'
      AND meetingId   = '$meetingId'
    LIMIT 1
");

if ($cekRes && mysqli_num_rows($cekRes) > 0) {
    $_SESSION['attendance_msg'] = "Kamu sudah absen untuk pertemuan ke-".$meeting['meetingNo'].".";
    header("Location: index.php?meeting=".$meetingId);
    exit;
}

// ------------------ AMBIL SESSION & TERM AKTIF ------------------
$sessionRes = mysqli_query($conn, "SELECT Id FROM tblsessionterm WHERE isActive = 1 LIMIT 1");
$sessionRow = $sessionRes ? mysqli_fetch_assoc($sessionRes) : null;
$sessionTermId = $sessionRow ? $sessionRow['Id'] : null;

if (!$sessionTermId) {
    $_SESSION['attendance_msg'] = "Absensi gagal: Session & Term aktif belum diset oleh admin.";
    header("Location: index.php");
    exit;
}

// ------------------ TENTUKAN STATUS ------------------
// 1 = Hadir; 0 = Alfa jika melewati meetingEndTime yang valid
$status = 1;
if (!empty($meeting['meetingEndTime']) && $meeting['meetingEndTime'] !== '00:00:00' && $nowTime > $meeting['meetingEndTime']) {
    $status = 0;
}

$nowDateTime = date('Y-m-d H:i:s');

// ------------------ INSERT ABSEN ------------------
$ins = mysqli_query($conn, "
    INSERT INTO tblattendance
      (admissionNo, classId, classArmId, sessionTermId, meetingId, status, dateTimeTaken)
    VALUES
      ('$admissionNo', '$classId', '$classArmId', '$sessionTermId', '$meetingId', '$status', '$nowDateTime')
");

if ($ins) {
    if ($status == 1) {
        $_SESSION['attendance_msg'] = "Absensi berhasil untuk pertemuan ke-".$meeting['meetingNo'].".";
    } else {
        $_SESSION['attendance_msg'] = "Kamu melewati batas waktu, tercatat Alfa untuk pertemuan ke-".$meeting['meetingNo'].".";
    }
} else {
    $_SESSION['attendance_msg'] = "Absensi gagal disimpan: " . mysqli_error($conn);
}

header("Location: index.php?meeting=".$meetingId);
exit;
