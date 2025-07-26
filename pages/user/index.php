<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';

// Filtering dan Searching
$filter_role = $_GET['filter_role'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM users WHERE 1";

if ($filter_role) {
  $query .= " AND role = '".mysqli_real_escape_string($conn, $filter_role)."'";
}

if ($search) {
  $search = mysqli_real_escape_string($conn, $search);
  $query .= " AND (nama LIKE '%$search%' OR email LIKE '%$search%')";
}

$query .= " ORDER BY id ASC";
$data = mysqli_query($conn, $query);

// Ambil semua data user
// $data = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kelola Pengguna</title>
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
      <h4 class="mb-3">Data Pengguna</h4>

      <form method="GET" class="row g-3 mb-3">
        <!-- filtering -->
        <div class="col md-3">
          <select name="filter_role" class="form-select">
            <option value="">-- Semua Role --</option>
            <option value="admin"<?= ($filter_role == 'admin') ? 'selected' : '' ?>>Admin</option>
            <option value="karyawan"<?= ($filter_role == 'karyawan') ? 'selected' : '' ?>>Karyawan</option>
          </select>
        </div>
        <!-- searchig -->
        <div class="col md-5">
          <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="<?= htmlspecialchars($search)?>">
        </div>
        <div class="col md-2">
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="index.php" class="btn btn-secondary">Reset</a>
        </div>
      </form>

      <a href="create.php" class="btn btn-primary mb-3">+ Tambah Pengguna</a>
      <a href="export_excel.php?role=karyawan" class="btn btn-success mb-3">Export Excel</a>
      <!-- <a href="export_excel.php?role=admin" class="btn btn-success mb-3">Download Admin</a>
      <a href="export_excel.php?role=karyawan" class="btn btn-success mb-3">Download Karyawan</a> -->

      <?php 
        $pdf_url = "export_pdf.php";
        $params = [];
        if(!empty($filter_role)) $params[] = 'role' . urlencode($filter_role);
        if(!empty($search)) $params[] = 'role' . urlencode($search);
        if($params) $pdf_url .= '?' . implode('&', $params);
      ?>
      <a href="<?= $pdf_url?>" class="btn btn-danger mb-3">Export PDF</a>
    
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
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
              <td><?= htmlspecialchars($row['nama']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= $row['role'] ?></td>
              <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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