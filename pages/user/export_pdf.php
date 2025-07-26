<?php
require '../../vendor/autoload.php';
use Dompdf\Dompdf;

include '../../config/database.php';

$roleFilter = $_GET['role'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM users WHERE 1";

if ($roleFilter) {
  $query .= " AND role = '".mysqli_real_escape_string($conn, $roleFilter)."'";
}

if ($search) {
  $search = mysqli_real_escape_string($conn, $search);
  $query .= " AND (nama LIKE '%$search%' OR email LIKE '%$search%')";
}

$query .= " ORDER BY id ASC";
$result = mysqli_query($conn, $query);

$html = '<h3 style="text-align:center;">Data Pengguna</h3>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
<thead>
<tr>
  <th>No</th>
  <th>Nama</th>
  <th>Email</th>
  <th>Role</th>
</tr>
</thead>
<tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
  $html .= '<tr>
    <td>' . $no++ . '</td>
    <td>' . htmlspecialchars($row['nama']) . '</td>
    <td>' . htmlspecialchars($row['email']) . '</td>
    <td>' . $row['role'] . '</td>
  </tr>';
}
$html .= '</tbody></table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("data_pengguna.pdf", array("Attachment" => 1));
?>
