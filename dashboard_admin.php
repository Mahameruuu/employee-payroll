<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: auth/login.php");
  exit;
}

require_once 'config/database.php';

// Statistik
$admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='admin'"))['total'];
$karyawan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='karyawan'"))['total'];
$agenda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agenda"))['total'];
// $gaji = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM gaji"))['total'];
$gaji = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_gaji) as total FROM gaji"))['total'];
$gaji = number_format($gaji, 0, ',', '.');

// Grafik total gaji per bulan
$gajiPerBulan = ['labels' => [], 'data' => []];
$query = mysqli_query($conn, "
  SELECT DATE_FORMAT(tanggal_gaji, '%b') AS bulan, SUM(total_gaji) as total 
  FROM gaji 
  GROUP BY MONTH(tanggal_gaji)
");
while ($row = mysqli_fetch_assoc($query)) {
  $gajiPerBulan['labels'][] = $row['bulan'];
  $gajiPerBulan['data'][] = (int)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- FullCalendar -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

  <!-- Sidebar -->
  <?php include 'layout/sidebar.php'; ?>

  <div class="content p-4">
    <!-- Navbar -->
    <?php include 'layout/navbar.php'; ?>

    <h2 class="mb-4">📊 Dashboard Admin</h2>

    <!-- Info Cards -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
          <div class="card-body">
            <h6 class="text-muted">👤 Total Admin</h6>
            <h3 class="text-primary"><?= $admin ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
          <div class="card-body">
            <h6 class="text-muted">👷‍♂️ Total Karyawan</h6>
            <h3 class="text-success"><?= $karyawan ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
          <div class="card-body">
            <h6 class="text-muted">💸 Total Slip Gaji per Tahun</h6>
            <h3 class="text-warning">Rp <?= $gaji ?></h3>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
          <div class="card-body">
            <h6 class="text-muted">📅 Agenda</h6>
            <h3 class="text-primary"><?= $agenda ?></h3>
          </div>
        </div>
      </div>    
    </div>


    <!-- Grafik & Kalender -->
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

  <!-- Grafik Gaji Script -->
  <script>
    const ctx = document.getElementById('grafikGaji');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($gajiPerBulan['labels']) ?>,
        datasets: [{
          label: 'Total Gaji (Rp)',
          data: <?= json_encode($gajiPerBulan['data']) ?>,
          backgroundColor: '#0d6efd'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return 'Rp ' + value.toLocaleString('id-ID');
              }
            }
          }
        }
      }
    });
  </script>

  <!-- Kalender Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 400,
        events: 'pages/kalendar/agenda_data.php'
      });
      calendar.render();
    });
  </script>


</body>
</html>