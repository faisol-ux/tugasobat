<?php
require_once 'admin_auth.php';
require_admin();
include 'koneksi.php';

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// PROSES TAMBAH (CREATE)
if ($aksi == 'tambah') {
    $id_pasien   = $_POST['id_pasien'];
    $id_dokter   = $_POST['id_dokter'];
    $tgl_periksa = $_POST['tgl_periksa'];
    $keluhan     = $_POST['keluhan'];
    $tensi_darah = $_POST['tensi_darah'];

    $sql = "INSERT INTO Pemeriksaan (id_pasien, id_dokter, tgl_periksa, keluhan, tensi_darah) 
            VALUES ('$id_pasien', '$id_dokter', '$tgl_periksa', '$keluhan', '$tensi_darah')";
    
    if (mysqli_query($koneksi, $sql)) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }
} 
// PROSES UPDATE
elseif ($aksi == 'update') {
    $id_periksa  = $_POST['id_periksa'];
    $id_pasien   = $_POST['id_pasien'];
    $id_dokter   = $_POST['id_dokter'];
    $tgl_periksa = $_POST['tgl_periksa'];
    $keluhan     = $_POST['keluhan'];
    $tensi_darah = $_POST['tensi_darah'];

    $sql = "UPDATE Pemeriksaan SET 
            id_pasien='$id_pasien', 
            id_dokter='$id_dokter', 
            tgl_periksa='$tgl_periksa', 
            keluhan='$keluhan', 
            tensi_darah='$tensi_darah' 
            WHERE id_periksa='$id_periksa'";
    
    if (mysqli_query($koneksi, $sql)) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }
}
?>