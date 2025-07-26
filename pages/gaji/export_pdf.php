<?php
require '../../vendor/autoload.php';
include '../../config/database.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$keyword = $_GET['keyword'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

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

ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export PDF Gaji</title>
    <style>
        body{ font-family: sans-serif;}
        table{width: 100; border-collapse: collapse; margin-top: 20px;}
        table, th, td{border: 1px solid black;}
        th, td{padding: 8px; text-align: center;}
        h3{text-align: center;}
    </style>
</head>
<body>
    <h3>Data Gaji Karyawan</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Gaji Pokok</th>
                <th>Tunjangan</th>
                <th>Potongan</th>
                <th>Total Gaji</th>
                <th>Tanggal Gaji</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama'])?></td>
                    <td><?= number_format($row['gaji_pokok']) ?></td>
                    <td><?= number_format($row['tunjangan']) ?></td>
                    <td><?= number_format($row['potongan']) ?></td>
                    <td><?= number_format($row['total_gaji']) ?></td>
                    <td><?= $row['tanggal_gaji'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$html = ob_get_clean();

// Generate PDF
// $options = new Options();
// $options->set('isRemoteEnabled', true);

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("data_gaji_karyawan.pdf", array("Attachment" => 1));
?>