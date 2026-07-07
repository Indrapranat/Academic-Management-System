<?php
session_start();

/**
 * Kalau sudah login, arahkan langsung ke dashboard sesuai role
 */
if (!empty($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: Admin/index.php");
        exit;
    }

    if ($_SESSION['role'] === 'teacher') {
        header("Location: ClassTeacher/index.php");
        exit;
    }

    if ($_SESSION['role'] === 'student') {
        header("Location: student/index.php");
        exit;
    }
}

/**
 * Kalau belum login, tampilkan landing page
 */
header("Location: Instant/index.html");
exit;
