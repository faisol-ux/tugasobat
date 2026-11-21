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

    $sql = "INSERT INTO dokter (username, password, nama_dokter, alamat, tgl_lahir, jenis, spesialis) 
            VALUES ('$username', '$password', '$nama_dokter', '$alamat', '$tgl_lahir', '$jenis', '$spesialis')";
    mysqli_query($koneksi, $sql);
    header("Location: index.php");
    exit;

// UNTUK UPDATE DATA (EDIT)
} elseif ($aksi == 'update') {
    $id_dokter   = $_POST['id_dokter'] ?? $_POST['id'] ?? '';
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
            jenis='$jenis', 
            spesialis='$spesialis' 
            WHERE id='$id_dokter' OR id_dokter='$id_dokter'";
            
    mysqli_query($koneksi, $sql);
    header("Location: index.php");
    exit;

// UNTUK HAPUS DATA
} elseif ($aksi == 'hapus') {
    if (isset($_GET['id'])) {
        $id_dokter = $_GET['id'];
        mysqli_query($koneksi, "DELETE FROM dokter WHERE id='$id_dokter' OR id_dokter='$id_dokter'");
    }
    header("Location: index.php");
    exit;
}
?>
