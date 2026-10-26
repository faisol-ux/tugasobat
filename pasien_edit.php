<?php
    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");

    $id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM pasien WHERE id_pasien='$id'");
    $data = mysqli_fetch_array($query);

    if (isset($_POST['simpan'])) {
        $no_registrasi = $_POST['no_registrasi'];
        $nama_pasien = $_POST['nama_pasien'];
        $alamat = $_POST['alamat'];
        $tgl_lahir = $_POST['tgl_lahir'];

        $update_query = "UPDATE pasien SET no_registrasi='$no_registrasi', nama_pasien='$nama_pasien', alamat='$alamat', tgl_lahir='$tgl_lahir' WHERE id_pasien='$id'";
        $insert = mysqli_query($conn, $update_query);

    header("Location: pasien_read.php");
    }
?>

<html>
    <head></head>
    <body>
        <form method='POST' action='pasien_edit.php?id_pasien=<?php echo $id; ?>'>
            <table>
                <tr>
                    <td>Nomer Registrasi</td>
                </tr>
                <tr>
                    <td><input type='text' name='no_registrasi' value='<?php echo $data['no_registrasi']; ?>'></td>
                </tr>
                <tr>
                    <td>Nama Pasien</td>
                </tr>
                <tr>
                    <td><input type='text' name='nama_pasien' value='<?php echo $data['nama_pasien']; ?>'></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                </tr>
                <tr>
                    <td><input type='text' name='alamat' value='<?php echo $data['alamat']; ?>'></td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                </tr>
                <tr>
                    <td><input type='date' name='tgl_lahir' value='<?php echo $data['tgl_lahir']; ?>'></td>
                </tr>
                 <tr>
                    <td><button type='submit' name='simpan'>Simpan</button></td>
                </tr>
            </table>
        </form>
    </body>
</html>