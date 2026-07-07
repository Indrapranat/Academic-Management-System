<?php
// ClassTeacher/Includes/session.php
require_once __DIR__ . '/../../Includes/session_bootstrap.php';

bootRoleSession('teacher');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    session_write_close();
    header("Location: /attendance-php/login.php");
    exit;
}

// Optional hard-check
if (empty($_SESSION['userId'])) {
    session_write_close();
    header("Location: /attendance-php/login.php");
    exit;
}
