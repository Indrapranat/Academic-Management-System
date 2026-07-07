<?php
require_once __DIR__ . '/../../Includes/session_bootstrap.php';

// Pastikan session student yang aktif (cookie berbeda)
bootRoleSession('student');

// Proteksi: wajib role student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
  header("Location: ../../login.php");
  exit;
}
