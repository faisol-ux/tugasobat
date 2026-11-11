<?php
if (isset($_POST['simpan'])) {
    $no_registrasi = $_POST['no_registrasi'];
    $nama_pasien = $_POST['nama_pasien'];
    $alamat = $_POST['alamat'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $koneksi = mysqli_connect("localhost", "root", "", "rumah sakit");

    $sql_create = "INSERT INTO pasien (no_registrasi, nama_pasien, alamat, tgl_lahir) VALUES ('$no_registrasi', '$nama_pasien', '$alamat', '$tgl_lahir')";

    if (mysqli_query($koneksi, $sql_create)) {
        header("Location: pasien_read.php");
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
    <form method='post' action='pasien_add.php'>
    <table>
            <tr>
                <td>Nomer Registrasi</td>
            </tr>
            <tr>
                <td><input type='text' name='no_registrasi' required></td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
            </tr>
            <tr>
                <td><input type='text' name='nama_pasien' required></td>
            </tr>
            <tr>
                <td>Alamat</td>
            </tr>
            <tr>
                <td><textarea name='alamat' required></textarea></td>
            </tr>
            <tr>
                <td>Tanggal Lahir</td>
            </tr>
            <tr>
                <td><input type='date' name='tgl_lahir' required></td>
            </tr>
            <tr>
                <td><button type='submit' name='simpan'>Simpan</button></td>
            </tr>
        </table>
    </form>
</body>
</html>