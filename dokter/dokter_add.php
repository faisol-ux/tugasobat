<?php
if (isset($_POST['simpan'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_dokter = $_POST['nama_dokter'];
    $alamat = $_POST['alamat'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $Spesialis = $_POST['Spesialis'];
    
    
    $koneksi = mysqli_connect("localhost", "root", "", "rumah sakit");

    

    $sql_create = "INSERT INTO dokter (username, password, nama_dokter, alamat, tgl_lahir, jenis_kelamin, Spesialis) VALUES ('$username', '$password', '$nama_dokter', '$alamat', '$tgl_lahir', '$jenis_kelamin', '$Spesialis')";

    if (mysqli_query($koneksi, $sql_create)) {
        header("Location: dokter_read.php");
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
    <form method='post' action='dokter_add.php'>
    <table>
            <tr>
                <td>Username</td>
            </tr>
            <tr>
                <td><input type='text' name='username' required></td>
            </tr>
            <tr>
                <td>Password</td>
            </tr>
            <tr>
                <td><input type='text' name='password' required></td>
            </tr>
            <tr>
                <td>Nama Dokter</td>
            </tr>
            <tr>
                <td><input type='text' name='nama_dokter' required></td>
            </tr>
            <tr>
                <td>Alamat</td>
            </tr>
            <tr>
                <td><input type='text' name='alamat' required></td>
            </tr>
            <tr>
                <td>Tanggal Lahir</td>
            </tr>
            <tr>
                <td><input type='date' name='tgl_lahir' required></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
            </tr>
            <tr>
                <td><select name='jenis_kelamin' required>
                    <option value='Laki-laki'>Laki-laki</option>
                    <option value='Perempuan'>Perempuan</option>
                </select></td>
            </tr>
            <tr>
                <td>Spesialis</td>
            </tr>
            <tr>
                <td><input type='text' name='Spesialis' required></td>
            </tr>
            <tr>
                <td><button type='submit' name='simpan'>Simpan</button></td>
            </tr>
        </table>
    </form>
</body>
</html>