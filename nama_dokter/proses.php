<?php
include 'koneksi.php';
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// UNTUK TAMBAH DATA
if ($aksi == 'tambah') {
    $username    = $_POST['username'];
    $password    = $_POST['password'];
    $nama_dokter = $_POST['nama_dokter'];
    $alamat      = $_POST['alamat'];
    $tgl_lahir   = $_POST['tgl_lahir'];
    $jenis       = $_POST['jenis'];
    $spesialis   = $_POST['spesialis'];

    $sql = "INSERT INTO dokter (username, password, nama_dokter, alamat, tgl_lahir, Jenis, Spesialis) 
            VALUES ('$username', '$password', '$nama_dokter', '$alamat', '$tgl_lahir', '$jenis', '$spesialis')";
    mysqli_query($koneksi, $sql);
    header("Location: index.php");

// UNTUK UPDATE DATA (EDIT)
} elseif ($aksi == 'update') {
    $id_dokter   = $_POST['id_dokter'];
    $username    = $_POST['username'];
    $password    = $_POST['password'];
    $nama_dokter = $_POST['nama_dokter'];
    $alamat      = $_POST['alamat'];
    $tgl_lahir   = $_POST['tgl_lahir'];
    $jenis       = $_POST['jenis'];
    $spesialis   = $_POST['spesialis'];

    $sql = "UPDATE dokter SET 
            username='$username', 
            password='$password', 
            nama_dokter='$nama_dokter', 
            alamat='$alamat', 
            tgl_lahir='$tgl_lahir', 
            Jenis='$jenis', 
            Spesialis='$spesialis' 
            WHERE id_dokter='$id_dokter'";
            
    mysqli_query($koneksi, $sql);
    header("Location: index.php");
}
?>