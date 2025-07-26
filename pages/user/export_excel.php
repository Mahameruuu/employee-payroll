<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

require '../../vendor/autoload.php'; 
include '../../config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();

// Roles yang ingin dipisah ke sheet masing-masing
$roles = ['admin', 'karyawan'];

foreach ($roles as $index => $role) {
    // Tambahkan sheet baru, kecuali untuk sheet pertama (default)
    if ($index > 0) { 
        $spreadsheet->createSheet();
    }
    
    $sheet = $spreadsheet->setActiveSheetIndex($index);
    $sheet->setTitle(ucfirst($role)); // Sheet: Admin / Karyawan

    // Header kolom
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Nama');
    $sheet->setCellValue('C1', 'Email');
    $sheet->setCellValue('D1', 'Role');

    // Query data sesuai role
    $query = mysqli_query($conn, "SELECT * FROM users WHERE role = '$role' ORDER BY id ASC");

    $rowNum = 2;
    $no = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        $sheet->setCellValue("A$rowNum", $no++);
        $sheet->setCellValue("B$rowNum", $row['nama']);
        $sheet->setCellValue("C$rowNum", $row['email']);
        $sheet->setCellValue("D$rowNum", $row['role']);
        $rowNum++; 
    }
}

// Kembalikan ke sheet pertama
$spreadsheet->setActiveSheetIndex(0);

// Output file ke browser
if (ob_get_length()) ob_end_clean();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="data_karyawan.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
