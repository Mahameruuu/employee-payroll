<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';

mysqli_query($conn, "DELETE FROM gaji");

header("Location: index.php");
exit;

?>