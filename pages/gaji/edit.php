<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM gaji WHERE id='$id'"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id     = $_POST['user_id'];
    $gaji_pokok  = $_POST['gaji_pokok'];
    $tunjangan   = $_POST['tunjangan'];
    $potongan    = $_POST['potongan'];
    $tanggal     = $_POST['tanggal_gaji'];
    $total       = $gaji_pokok + $tunjangan - $potongan;

    mysqli_query($conn, "UPDATE gaji SET 
        user_id='$user_id', 
        gaji_pokok='$gaji_pokok', 
        tunjangan='$tunjangan', 
        potongan='$potongan', 
        total_gaji='$total', 
        tanggal_gaji='$tanggal' 
        WHERE id='$id'");

    header("Location: index.php");
    exit;
}

$karyawan = mysqli_query($conn, "SELECT * FROM users WHERE role='karyawan'");

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
        <h4 class="mb-3">Edit Data Gaji</h4>

        <form action="" method="post" class="card p-4 shadow-sm border-0">
            <div class="mb-3">
                <label class="form-label">Karyawan</label>
                <select name="user_id" class="form-select" required>
                    <?php while ($k= mysqli_fetch_assoc($karyawan)) : ?>
                        <option value="<?= $k['id'] ?>"<?= $k['id'] == $data['user_id'] ? 'selected' : '' ?>>  
                            <?= $k['nama'] ?>   
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Gaji Pokok</label>
                <input type="number" name="gaji_pokok" value="<?= $data['gaji_pokok'] ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tunjangan</label>
                <input type="number" name="tunjangan" value="<?= $data['tunjangan'] ?>" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Potongan</label>
                <input type="number" name="potongan" value="<?= $data['potongan'] ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Gaji</label>
                <input type="date" name="tanggal_gaji" value="<?= $data['tanggal_gaji'] ?>" class="form-control" required>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>

    <?php include '../../layout/footer.php'; ?>
  </div>

</body>
</html>
