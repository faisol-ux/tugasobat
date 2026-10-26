<?php
include 'koneksi.php';

$id = $_GET['id'];

$sql = "DELETE FROM resep_obat WHERE id_resep_obat='$id'";

if (mysqli_query($koneksi, $sql)) {
    header("Location: index.php");
} else {
    echo "Error menghapus data: " . mysqli_error($koneksi);
}
?>