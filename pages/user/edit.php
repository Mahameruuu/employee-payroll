<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

include '../../config/database.php';

$id = $_GET['id'];
$user = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$data = mysqli_fetch_assoc($user);

if(!$data){
    echo "User tidak ditemukan!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = $_POST['role'];

    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET nama='$nama', email='$email', password='$password', role='$role' WHERE id='$id'");
    }else{
        mysqli_query($conn, "UPDATE users SET nama='$nama', email='$email', role='$role' WHERE id='$id'");
    }
    
    header("Location: index.php");
    exit;
}

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
        <h4 class="mb-3">Edit Data Karyawan</h4>

        <form action="" method="post" class="card p-4 shadow-sm border-0">
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" class="form-control" required>
            </div>

            <!-- <div class="mb-3">
                <label class="form-label">Password (Kosongkan jika tidak ingin mengganti)</label>
                <input type="password" name="password" class="form-control" readonly>
            </div> -->
            
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" id="" class="form-select" required>
                    <option value="admin" <?= $data['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="karyawan" <?= $data['role'] == 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
                </select>
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
