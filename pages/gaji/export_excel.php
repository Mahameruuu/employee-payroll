<?php

require '../../vendor/autoload.php'; 
include '../../config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Data Gaji');

// Header column
$sheet->fromArray(['No', 'Nama', 'Gaji Pokok', 'Tunjangan', 'Potongan', 'Total Gaji', 'Tanggal Gaji'], NULL, 'A1');
$rowIndex = 2;
$no = 1;
while ($row = mysqli_fetch_assoc($data)) {
    $sheet->setCellValue("A$rowIndex", $no++);
    $sheet->setCellValue("B$rowIndex", $row['nama']);
    $sheet->setCellValue("C$rowIndex", $row['gaji_pokok']);
    $sheet->setCellValue("D$rowIndex", $row['tunjangan']);
    $sheet->setCellValue("E$rowIndex", $row['potongan']);
    $sheet->setCellValue("F$rowIndex", $row['total_gaji']);
    $sheet->setCellValue("G$rowIndex", $row['tanggal_gaji']);
    $rowIndex++; 
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="data_gaji.xlsx"');
header('Cache-Control: max-age=0');

ob_clean();
flush();

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
