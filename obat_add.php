<?php
if (isset($_POST['simpan'])) {
    $kode_obat = $_POST['kode_obat'];
    $nama_obat = $_POST['nama_obat'];
    
    $koneksi = mysqli_connect("localhost", "root", "", "rumah sakit");

    $sql_create = "INSERT INTO obat (kode_obat, nama_obat) VALUES ('$kode_obat', '$nama_obat')";

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
    <title></title>
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
                <td><button type='submit' name='simpan'>Simpan</button></td>
            </tr>
        </table>
    </form>
</body>
</html>