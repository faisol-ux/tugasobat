<?php

    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    $id = isset($_GET['id_dokter']) ? (int) $_GET['id_dokter'] : 0;
    $query = mysqli_query($conn, "SELECT * FROM dokter WHERE id_dokter=" . $id);
    if (!$query) {
        die("Gagal mengambil data dokter: " . mysqli_error($conn));
    }
    $data = mysqli_fetch_array($query);

    if (!$data) {
        die('Data dokter tidak ditemukan. <a href="dokter_read.php">Kembali ke daftar dokter</a>');
    }

    if (isset($_POST['simpan'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $nama_dokter = $_POST['nama_dokter'];
        $alamat = $_POST['alamat'];
        $tgl_lahir = $_POST['tgl_lahir'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $Spesialis = $_POST['Spesialis'];

        $update_query = "UPDATE dokter SET username='$username', password='$password', nama_dokter='$nama_dokter', alamat='$alamat', tgl_lahir='$tgl_lahir', jenis_kelamin='$jenis_kelamin', Spesialis='$Spesialis' WHERE id_dokter=" . $id;
        if (!mysqli_query($conn, $update_query)) {
            die("Gagal menyimpan perubahan: " . mysqli_error($conn));
        }

        header("Location: dokter_read.php");
        exit;
    }
?>

<html>
    <head></head>
    <body>
        <form method='POST' action='dokter_edit.php?id_dokter=<?php echo $id; ?>'>
            <table>
                <tr>
                    <td>Username</td>
                </tr>
                <tr>
                    <td><input type='text' name='username' value='<?php echo $data['username']; ?>'></td>
                </tr>
                <tr>
                    <td>Password</td>
                </tr>
                <tr>
                    <td><input type='password' name='password' value='<?php echo $data['password']; ?>'></td>
                </tr>
                <tr>
                    <td>Nama dokter</td>
                </tr>
                <tr>
                    <td><input type='text' name='nama_dokter' value='<?php echo $data['nama_dokter']; ?>'></td>
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
                    <td>Jenis Kelamin</td>
                </tr>
                <tr>
                    <td>
                        <select name='jenis_kelamin'>
                            <option value='Laki-laki' <?php echo $data['jenis_kelamin'] === 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value='Perempuan' <?php echo $data['jenis_kelamin'] === 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Spesialis</td>
                </tr>
                <tr>
                    <td><input type='text' name='Spesialis' value='<?php echo $data['Spesialis']; ?>'></td>
                </tr>
                <tr>
                    <td><button type='submit' name='simpan'>Simpan</button></td>
                </tr>
            </table>
        </form>
    </body>
</html>