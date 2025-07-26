<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];

  $stmt = $conn->prepare("INSERT INTO agenda (title, description, start_date, end_date) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $title, $description, $start_date, $end_date);

  if ($stmt->execute()) {
    header("Location: index.php");
    exit;
  } else {
    $error = "Gagal menyimpan agenda.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buat Agenda</title>
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
      <h4 class="mb-3">📌 Buat Agenda Baru</h4>

      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" class="card p-4 shadow-sm border-0">
        <div class="mb-3">
          <label for="title" class="form-label">Judul Agenda</label>
          <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Deskripsi</label>
          <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>

        <div class="mb-3">
          <label for="start_date" class="form-label">Tanggal Mulai</label>
          <input type="date" class="form-control" id="start_date" name="start_date" required>
        </div>

        <div class="mb-3">
          <label for="end_date" class="form-label">Tanggal Selesai</label>
          <input type="date" class="form-control" id="end_date" name="end_date" required>
        </div>

        <div class="d-flex justify-content-between">
          <button type="submit" class="btn btn-success">Simpan Agenda</button>
          <a href="index.php" class="btn btn-secondary">Kembali</a>
        </div>
      </form>
    </div>

    <?php include '../../layout/footer.php'; ?>
  </div>

</body>
</html>
