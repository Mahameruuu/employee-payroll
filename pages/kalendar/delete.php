<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

require_once '../../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
  $conn->query("DELETE FROM agenda WHERE id = $id");
}

header("Location: index.php?message=Agenda berhasil dihapus");
exit;
