<?php
include 'koneksi.php';

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';


if ($aksi == 'tambah') {
    $id_periksa = $_POST['id_periksa'];
    $id_obat    = $_POST['id_obat'];

    $sql = "INSERT INTO resep_obat (id_periksa, id_obat) VALUES ('$id_periksa', '$id_obat')";
    
    if (mysqli_query($koneksi, $sql)) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }
}

elseif ($aksi == 'update') {
    $id_resep_obat = $_POST['id_resep_obat'];
    $id_periksa    = $_POST['id_periksa'];
    $id_obat       = $_POST['id_obat'];

    $sql = "UPDATE resep_obat SET 
            id_periksa='$id_periksa', 
            id_obat='$id_obat' 
            WHERE id_resep_obat='$id_resep_obat'";
    
    if (mysqli_query($koneksi, $sql)) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }
}
?>