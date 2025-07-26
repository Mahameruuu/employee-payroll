<?php 

$host = "localhost";
$user = "root";
$password = "";
$database = "test";

$conn = mysqli_connect("$host", "$user", "$password", "$database"); 

// opsional (untuk memberikan informasi ketika error atau tidak terkonek dengan db kita)
if(!$conn){
    die("Koneksi Gagal:" . mysqli_connect_error());
}

?>  