<?php
require_once '../config/database.php';
require_once 'middleware.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    check_csrf();
    $username = trim($_POST['nama']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE nama = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($user = $result->fetch_assoc()){
        if(password_verify($password, $user['password'])){
            session_regenerate_id(true);
            $_SESSION['id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            if($user['role'] === 'admin'){
                header('Location: ../dashboard_admin.php');
                exit;
            } else {
                header('Location: ../dashboard_karyawan.php');
                exit;
            }
        }
    }
    $error = 'Username atau password salah.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title mb-4 text-center">Login</h3>

          <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>

          <form method="post">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="mb-3">
              <label class="form-label">Username/Email</label>
              <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>

            <div class="mt-3 text-center">
              <a href="register.php">Belum punya akun? Daftar di sini</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
