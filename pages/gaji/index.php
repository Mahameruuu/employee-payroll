<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';

// Filtering dan Searching
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

$query = "SELECT gaji.*, users.nama AS nama 
          FROM gaji
          JOIN users ON gaji.user_id = users.id
          WHERE 1=1";

if(!empty($keyword)){
  $query .= " AND users.nama LIKE '%" . mysqli_real_escape_string($conn, $keyword) . "%'";
}

if(!empty($bulan)){
  $query .= " AND MONTH(tanggal_gaji) = " . intval($bulan);
}

if(!empty($tahun)){
  $query .= " AND YEAR(tanggal_gaji) = " . intval($tahun);
}

$query .= " ORDER BY tanggal_gaji DESC";
$data = mysqli_query($conn, $query);

// $data = mysqli_query($conn, "SELECT gaji.*, users.nama AS nama FROM gaji JOIN users ON gaji.user_id = users.id
//                     ORDER BY tanggal_gaji DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kelola Gaji</title>
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
      <h4 class="mb-3">Data Gaji Karyawan</h4>
 
      <form method="GET" class="row mb-4">
        <div class="col md-3">
          <input type="text" name="keyword" class="form-control" placeholder="Cari nama..." value="<?= htmlspecialchars($keyword)?>">
        </div>
        <div class="col-md-2">
          <select name="bulan" class="form-control">
            <option value="">-- Bulan --</option>
            <?php
            for($i = 1; $i<=12; $i++){
              echo "<option value='$i' $selected>" . date('F', mktime(0,0,0,$i, 10)) . "</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-2">
          <select name="tahun" class="form-control">
            <option value="">-- Tahun --</option>
            <?php
            $tahunSekarang = date('Y');
            for($t = $tahunSekarang; $t>=$tahunSekarang - 5; $t--){
              $selected = ($t==$tahun) ? 'selected' : '';
              echo "<option value='$t' $selected>$t</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary" type="submit">Filter</button>
          <a href="index.php" class="btn btn-secondary">Reset</a>
        </div>

      </form>

      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex gap-2">
          <a href="create.php" class="btn btn-primary">+ Tambah Gaji</a>
          <a href="export_excel.php" class="btn btn-success">Export Excel</a>
          <a href="export_pdf.php" class="btn btn-danger">Export PDF</a>
        </div>
        <a href="delete_all.php" class="btn btn-danger mb-3" onclick="return confirm('Yakin ingin menghapus semua data?')">Hapus Semua Gaji Anggota</a>
      </div>

      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Gaji Pokok</th>
            <th>Tunjangan</th>
            <th>Potongan</th>
            <th>Total Gaji</th>
            <th>Tanggal Gaji</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $no=1; 
            while ($row = mysqli_fetch_assoc($data)) :
          ?>
            <tr>
              <td><?=$no++?></td>
              <td><?=htmlspecialchars($row['nama'])?></td>
              <td><?=number_format($row['gaji_pokok'])?></td>
              <td><?=number_format($row['tunjangan'])?></td>
              <td><?=number_format($row['potongan'])?></td>
              <td><?=number_format($row['total_gaji'])?></td>
              <td><?=$row['tanggal_gaji']?></td>
              <td>
                <a href="edit.php?id=<?=$row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete.php?id=<?=$row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">Hapus</a>
              </td>
            </tr>
            <?php endwhile?>
        </tbody>
      </table>
    </div>

    <?php include '../../layout/footer.php'; ?>
  </div>

</body>
</html>
