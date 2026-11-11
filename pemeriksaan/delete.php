<?php
require_once 'admin_auth.php';
require_admin();
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM Pemeriksaan WHERE id_periksa='$id'";
    
    if (mysqli_query($koneksi, $sql)) {
        header("Location: index.php");
    } else {
        echo "Error menghapus data: " . mysqli_error($koneksi);
    }
} else {
    header("Location: index.php");
}
?>