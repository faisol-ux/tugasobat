<?php
if (isset($_POST['simpan'])) {
    $kode_obat = $_POST['kode_obat'];
    $nama_obat = $_POST['nama_obat'];
    $frekuensi_peresepan = isset($_POST['frekuensi_peresepan']) ? (int)$_POST['frekuensi_peresepan'] : 0;
    
    $koneksi = mysqli_connect("localhost", "root", "", "rumah sakit");

    $sql_create = "INSERT INTO obat (kode_obat, nama_obat, frekuensi_peresepan) VALUES ('$kode_obat', '$nama_obat', $frekuensi_peresepan)";

    if (mysqli_query($koneksi, $sql_create)) {
        header("Location: obat_read.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }

    mysqli_close($koneksi);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tambah Obat</title>
</head>
<body>
    <form method='post' action='obat_add.php'>
        <table>
            <tr>
                <td>Kode Obat</td>
            </tr>
            <tr>
                <td><input type='text' name='kode_obat' required></td>
            </tr>
            <tr>
                <td>Nama Obat</td>
            </tr>
            <tr>
                <td><input type='text' name='nama_obat' required></td>
            </tr>
            <tr>
                <td>Frekuensi Peresepan</td>
            </tr>
            <tr>
                <td><input type='number' name='frekuensi_peresepan' value='0' min='0' required></td>
            </tr>
            <tr>
                <td><button type='submit' name='simpan'>Simpan</button></td>
            </tr>
        </table>
    </form>
</body>
</html>
