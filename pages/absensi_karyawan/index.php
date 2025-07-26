<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'karyawan') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';

$nama = $_SESSION['nama'];

// Ambil ID user berdasarkan nama session
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE nama='$nama'"));
$user_id = $user['id'];

// Ambil data absensi user tersebut
$data = mysqli_query($conn, "SELECT * FROM absensi WHERE user_id = $user_id ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Absensi Saya</title>
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

  <?php include '../../layout/sidebar_karyawan.php'; ?>

  <div class="content">
    <?php include '../../layout/navbar_karyawan.php'; ?>

    <div class="container mt-4">
      <h4 class="mb-3">Riwayat Absensi Saya</h4>

      <div class="alert alert-info">
        <i class="bi bi-info-circle-fill"></i>
        Berikut adalah riwayat absensi berdasarkan waktu masuk dan pulang Anda. Periksa apakah Anda hadir tepat waktu.
      </div>

      <a href="create.php" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Absensi
      </a>

      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Status Masuk</th>
            <th>Jam Pulang</th>
            <th>Status Pulang</th>
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
              <td><?= $row['jam_masuk'] ?? '-' ?></td>
              <td>
                <?php
                  if ($row['jam_masuk']) {
                    $jam_masuk = strtotime($row['jam_masuk']);
                    $batas_masuk = strtotime('08:10:00'); // jam kerja 08:00 + toleransi 10 menit

                    if ($jam_masuk <= $batas_masuk) {
                      echo "<span class='badge bg-success'>Tepat Waktu</span>";
                    } else {
                      echo "<span class='badge bg-warning text-dark'>Terlambat</span>";
                    }
                  } else {
                    echo "-";
                  }
                ?>
              </td>
              <td><?= $row['jam_pulang'] ?? '-' ?></td>
              <td>
                <?php
                  if ($row['jam_pulang']) {
                    $jam_pulang = strtotime($row['jam_pulang']);
                    $batas_pulang = strtotime('17:00:00'); // jam kerja selesai pukul 17:00

                    if ($jam_pulang >= $batas_pulang) {
                      echo "<span class='badge bg-success'>Tepat Waktu</span>";
                    } else {
                      echo "<span class='badge bg-warning text-dark'>Pulang Cepat</span>";
                    }
                  } else {
                    echo "-";
                  }
                ?>
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
