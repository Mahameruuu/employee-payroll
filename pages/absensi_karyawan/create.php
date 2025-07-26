<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'karyawan') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';
date_default_timezone_set('Asia/Jakarta');

$user_id = (int) $_SESSION['id'];
$tanggal_hari_ini = date('Y-m-d');
$jam_sekarang = date('H:i:s');

// Ambil jadwal absensi hari ini
$query_jadwal = mysqli_query($conn, "SELECT * FROM jadwal_absensi WHERE tanggal = '$tanggal_hari_ini'");
$jadwal = mysqli_fetch_assoc($query_jadwal);

// Ambil absensi user hari ini
$query_absen = mysqli_query($conn, "SELECT * FROM absensi WHERE user_id = $user_id AND tanggal = '$tanggal_hari_ini'");
$absen = mysqli_fetch_assoc($query_absen);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $jadwal) {
  // Absen Masuk
  if (isset($_POST['absen_masuk']) && !$absen) {
    $awal = $jadwal['toleransi_masuk_awal'];
    $akhir = $jadwal['toleransi_masuk_akhir'];

    if ($jam_sekarang >= $awal && $jam_sekarang <= $akhir) {
      $status = ($jam_sekarang <= $jadwal['toleransi_masuk_akhir']) ? 'Tepat Waktu' : 'Terlambat';

      $insert = mysqli_query($conn, 
        "INSERT INTO absensi (user_id, tanggal, jam_masuk, status_masuk) 
         VALUES ($user_id, '$tanggal_hari_ini', '$jam_sekarang', '$status')");

      $message = $insert ? "✅ Absen masuk berhasil ($status)" : "❌ Gagal menyimpan absen masuk.";
    } else {
      $message = "⚠️ Waktu absen masuk di luar batas toleransi.";
    }
  }

  // Refresh data absen
  $absen = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT * FROM absensi WHERE user_id = $user_id AND tanggal = '$tanggal_hari_ini'"));

  // Absen Pulang
  if (isset($_POST['absen_pulang']) && $absen && !$absen['jam_pulang']) {
    $akhir_pulang = $jadwal['toleransi_pulang_akhir'];
    $jam_kerja_selesai = '17:00:00';

    if ($jam_sekarang <= $akhir_pulang) {
      $status = ($jam_sekarang <= $jam_kerja_selesai) ? 'Tepat Waktu' : 'Melewati Jam Kerja';

      $update = mysqli_query($conn, 
        "UPDATE absensi 
         SET jam_pulang = '$jam_sekarang', status_pulang = '$status' 
         WHERE user_id = $user_id AND tanggal = '$tanggal_hari_ini'");

      $message = $update ? "✅ Absen pulang berhasil ($status)" : "❌ Gagal menyimpan absen pulang.";
    } else {
      $message = "⚠️ Batas waktu absen pulang telah lewat.";
    }
  }

  // Refresh ulang
  $absen = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT * FROM absensi WHERE user_id = $user_id AND tanggal = '$tanggal_hari_ini'"));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Absensi Hari Ini</title>
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
    <h4 class="mb-4">📅 Absensi Hari Ini <small class="text-muted">(<?= date('d M Y') ?>)</small></h4>

    <?php if ($message): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <?php if (!$jadwal): ?>
      <div class="alert alert-warning">⚠️ Belum ada jadwal absensi untuk hari ini.</div>
    <?php else: ?>
      <div class="mb-4">
        <ul class="list-group">
          <li class="list-group-item">
            <strong>Jam Masuk:</strong> <?= $jadwal['toleransi_masuk_awal'] ?> - <?= $jadwal['toleransi_masuk_akhir'] ?>
          </li>
          <li class="list-group-item">
            <strong>Jam Pulang Maksimal:</strong> <?= $jadwal['toleransi_pulang_akhir'] ?>
          </li>
          <li class="list-group-item">
            <strong>Waktu Sekarang:</strong> <?= $jam_sekarang ?>
          </li>
        </ul>
      </div>

      <form method="POST">
        <!-- Absen Masuk -->
        <?php if (!$absen): ?>
          <button type="submit" name="absen_masuk" class="btn btn-success me-2 mb-2">
            <i class="bi bi-box-arrow-in-right"></i> Absen Masuk
          </button>
        <?php else: ?>
          <p>
            ✅ <strong>Absen Masuk:</strong> <?= $absen['jam_masuk'] ?>
            <span class="badge bg-<?= $absen['status_masuk'] === 'Tepat Waktu' ? 'success' : 'danger' ?>">
              <?= $absen['status_masuk'] ?>
            </span>
          </p>
        <?php endif; ?>

        <!-- Absen Pulang -->
        <?php if ($absen && !$absen['jam_pulang']): ?>
          <button type="submit" name="absen_pulang" class="btn btn-danger mt-2">
            <i class="bi bi-box-arrow-left"></i> Absen Pulang
          </button>
        <?php elseif ($absen && $absen['jam_pulang']): ?>
          <p class="mt-2">
            ✅ <strong>Absen Pulang:</strong> <?= $absen['jam_pulang'] ?>
            <span class="badge bg-<?= $absen['status_pulang'] === 'Tepat Waktu' ? 'success' : 'warning' ?>">
              <?= $absen['status_pulang'] ?>
            </span>
          </p>
        <?php endif; ?>
      </form>
    <?php endif; ?>

    <!-- Tombol Kembali -->
    <a href="index.php" class="btn btn-secondary mt-4">
      <i class="bi bi-arrow-left-circle"></i> Kembali 
    </a>
  </div>

  <?php include '../../layout/footer.php'; ?>
</div>

</body>
</html>
