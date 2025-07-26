<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';
$data = mysqli_query($conn, "SELECT * FROM jadwal_absensi ORDER BY tanggal ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Jadwal Absensi</title>
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
      <h4 class="mb-3">Jadwal Absensi</h4>
      <div class="alert alert-info">
        <i class="bi bi-info-circle-fill"></i>
        Jam kerja standar adalah <strong>08:00</strong> untuk masuk dan <strong>17:00</strong> untuk pulang.
        Jadwal dapat disesuaikan jika ada perubahan.
      </div>

      <a href="create.php" class="btn btn-primary mb-3">+ Tambah Jadwal</a>

      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Toleransi Masuk</th>
            <th>Toleransi Pulang</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $no = 1; 
            while ($row = mysqli_fetch_assoc($data)) :
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= date("d-m-Y", strtotime($row['tanggal'])) ?></td>
              <td><?= $row['toleransi_masuk_awal'] ?> – <?= $row['toleransi_masuk_akhir'] ?></td>
              <td>Jam 17.00 sampai <?= $row['toleransi_pulang_akhir'] ?></td>
              <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile ?>
        </tbody>
      </table>
    </div>

    <?php include '../../layout/footer.php'; ?>
  </div>

</body>
</html>
