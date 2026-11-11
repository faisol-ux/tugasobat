<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "rumah sakit"; // Pastikan nama database benar (tanpa spasi)

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>