<?php

session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';

$id = $_GET['id'] ?? null;

if($id){
    mysqli_query($conn, "DELETE FROM gaji WHERE id='$id'");
}

header("Location: index.php");
exit;

?>