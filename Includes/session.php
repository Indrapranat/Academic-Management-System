<?php
require_once __DIR__ . '/session_bootstrap.php';

// Root session (kalau file ini dipakai umum, biarkan default)
bootRoleSession('public');

// Optional: kalau halaman ini memang wajib login:
if (!isset($_SESSION['role'])) {
  header("Location: ../login.php");
  exit;
}
