<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tanggal = $_POST['tanggal'];
  $masuk_awal = $_POST['masuk_awal'];
  $masuk_akhir = $_POST['masuk_akhir'];
  $pulang_akhir = $_POST['pulang_akhir'];

  $stmt = $conn->prepare("INSERT INTO jadwal_absensi 
    (tanggal, toleransi_masuk_awal, toleransi_masuk_akhir, toleransi_pulang_akhir) 
    VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $tanggal, $masuk_awal, $masuk_akhir, $pulang_akhir);
  $stmt->execute();
  $stmt->close();

  header("Location: index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Jadwal Absensi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
  <style>
    body {
      min-height: 100vh;
      display: flex;
    }
    .content {
      flex-grow: 1;
      background-color: #f8f9fa;
      padding: 20px;
      display: flex;
      flex-direction: column;
    }
    .footer {
      margin-top: auto;
      text-align: center;
      color: #6c757d;
      padding: 10px 0;
    }
  </style>
</head>
<body>

  <?php include '../../layout/sidebar.php'; ?>

  <div class="content">
    <?php include '../../layout/navbar.php'; ?>

    <div class="container mt-4">
      <h4 class="mb-4">Tambah Jadwal Absensi</h4>

      <form method="POST">
        <!-- Tanggal -->
        <div class="mb-3">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" required>
        </div>

        <!-- Toleransi Masuk Awal -->
        <div class="mb-3">
          <label class="form-label">Toleransi Masuk Awal</label>
          <input type="time" name="masuk_awal" class="form-control" required>
        </div>

        <!-- Toleransi Masuk Akhir -->
        <div class="mb-3">
          <label class="form-label">Toleransi Masuk Akhir</label>
          <input type="time" name="masuk_akhir" class="form-control" required>
        </div>

        <!-- Toleransi Pulang Maksimal -->
        <div class="mb-3">
          <label class="form-label">Toleransi Pulang Maksimal</label>
          <input type="time" name="pulang_akhir" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
      </form>
    </div>

    <?php include '../../layout/footer.php'; ?>
  </div>

</body>
</html>
