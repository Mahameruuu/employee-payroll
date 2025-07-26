<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';

$search = $_GET['search'] ?? '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

$query = "SELECT * FROM agenda WHERE 1";

if(!empty($search)){
  $search = mysqli_real_escape_string($conn, $search);
  $query .= " AND (title LIKE '%$search%')";
}

if(!empty($bulan)){
  $query .= " AND MONTH(start_date) = " . intval($bulan);
}

if(!empty($tahun)){
  $query .= " AND YEAR(start_date) = " . intval($tahun);
}

$query .= " ORDER BY id ASC";
$data = mysqli_query($conn, $query);

// Ambil data agenda dari database
// $data = mysqli_query($conn, "SELECT * FROM agenda ORDER BY start_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kelola Agenda</title>
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
      <h4 class="mb-3">Data Agenda</h4>

      <form method="GET" class="row mb-4">
        <div class="col md-3">
          <input type="text" name="search" class="form-control" placeholder="Cari Judul..." value="<?= htmlspecialchars($search)?>">
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

      <div class="d-flex justify-content-between">
        <a href="create.php" class="btn btn-primary mb-3">+ Tambah Agenda</a>
        <a href="delete_all.php" class="btn btn-danger mb-3" onclick="return confirm('Yakin ingin menghapus semua agenda?')">Hapus Semua Agenda</a>
      </div>

      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
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
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= htmlspecialchars($row['description']) ?></td>
              <td><?= $row['start_date'] ?></td>
              <td><?= $row['end_date'] ?></td>
              <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin ingin menghapus agenda ini?')">Hapus</a>
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
