<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';

// Ambil data berdasarkan ID
$id = $_GET['id'];
// $query = mysqli_query($conn, "SELECT * FROM jadwal_absensi WHERE id ='$id'");
$query = $conn->prepare("SELECT * FROM jadwal_absensi WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tanggal = $_POST['tanggal'];
  $masuk_awal = $_POST['masuk_awal'];
  $masuk_akhir = $_POST['masuk_akhir'];
  $pulang_akhir = $_POST['pulang_akhir'];

  $stmt = $conn->prepare("UPDATE jadwal_absensi SET tanggal=?, toleransi_masuk_awal=?, toleransi_masuk_akhir=?, toleransi_pulang_akhir=? WHERE id=?");
  $stmt->bind_param("ssssi", $tanggal, $masuk_awal, $masuk_akhir, $pulang_akhir, $id);
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
  <title>Edit Jadwal Absensi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
      <h4 class="mb-4">Edit Jadwal Absensi</h4>

      <div class="alert alert-info">
        <i class="bi bi-info-circle-fill"></i>
        Jam kerja standar adalah <strong>08:00</strong> untuk masuk dan <strong>17:00</strong> untuk pulang.
        Jadwal dapat disesuaikan jika ada perubahan.
      </div>

      <form method="POST">
        <!-- Tanggal -->
        <div class="mb-3">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal'] ?>" required>
        </div>

        <!-- Toleransi Masuk Awal -->
        <div class="mb-3">
          <label class="form-label">Toleransi Masuk Awal</label>
          <input type="time" name="masuk_awal" class="form-control" value="<?= $row['toleransi_masuk_awal'] ?>" required>
        </div>

        <!-- Toleransi Masuk Akhir -->
        <div class="mb-3">
          <label class="form-label">Toleransi Masuk Akhir</label>
          <input type="time" name="masuk_akhir" class="form-control" value="<?= $row['toleransi_masuk_akhir'] ?>" required>
        </div>

        <!-- Toleransi Pulang Akhir -->
        <div class="mb-3">
          <label class="form-label">Toleransi Pulang Maksimal</label>
          <input type="time" name="pulang_akhir" class="form-control" value="<?= $row['toleransi_pulang_akhir'] ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
      </form>
    </div>

    <?php include '../../layout/footer.php'; ?>
  </div>

</body>
</html>
