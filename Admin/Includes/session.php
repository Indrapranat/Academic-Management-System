<?php
require_once __DIR__ . '/../../Includes/session_bootstrap.php';

bootRoleSession('admin');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../login.php");
  exit;
}
