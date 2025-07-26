<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'karyawan') {
  header("Location: auth/login.php");
  exit;
}

require_once 'config/database.php';

$nama = $_SESSION['nama'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE nama='$nama'"));
$user_id = $user['id'];

// Total absensi (jumlah data absensi user)
$absensi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE user_id = $user_id"))['total'];

// Total gaji user
$total_gaji = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_gaji) as total FROM gaji WHERE user_id = $user_id"))['total'] ?? 0;

// Data gaji per bulan (untuk chart)
$data_gaji = mysqli_query($conn, "
  SELECT DATE_FORMAT(tanggal_gaji, '%M %Y') as bulan, SUM(total_gaji) as total 
  FROM gaji 
  WHERE user_id = $user_id 
  GROUP BY MONTH(tanggal_gaji), YEAR(tanggal_gaji)
  ORDER BY tanggal_gaji ASC
");

$bulan = [];
$nilai = [];
while ($row = mysqli_fetch_assoc($data_gaji)) {
  $bulan[] = $row['bulan'];
  $nilai[] = $row['total'];
}

$tanggal_gaji = date("l, d F Y");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Karyawan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <style>
    #calendar {
      max-width: 100%;
      margin: 20px auto;
    }
  </style>
</head>
<body>

  <?php include 'layout/sidebar_karyawan.php'; ?>

  <div class="content">
    <?php include 'layout/navbar_karyawan.php'; ?>

    <div class="container mt-4">
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
              <h6>📅 Total Absensi</h6>
              <h3><?= $absensi ?></h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
              <h6>💸 Total Gaji</h6>
              <h3>Rp <?= number_format($total_gaji, 0, ',', '.') ?></h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
              <h6>📆 Hari Ini</h6>
              <h4><?= $tanggal_gaji ?></h4>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <h6 class="card-title">📊 Total Gaji per Bulan</h6>
            <canvas id="grafikGaji"></canvas>
          </div>
        </div>
      </div>

      <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <h6 class="card-title">🗓️ Kalender Agenda</h6>
            <div id="calendar"></div>
          </div>
        </div>
      </div>
    </div>

    <?php include 'layout/footer.php'; ?>
  </div>

  <script>
    // Grafik Gaji
    const ctx = document.getElementById('grafikGaji').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($bulan) ?>,
        datasets: [{
          label: 'Total Gaji',
          data: <?= json_encode($nilai) ?>,
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Kalender (Dummy Events)
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 500,
        events: [
          {
            title: 'Gajian',
            start: '<?= date("Y-m") ?>-25',
            color: '#28a745'
          },
          {
            title: 'Absensi Terakhir',
            start: '<?= date("Y-m-d") ?>',
            color: '#ffc107'
          }
        ]
      });
      calendar.render();
    });
  </script>
</body>
</html>
