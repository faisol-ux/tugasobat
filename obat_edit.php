<?php

    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    $id = isset($_GET['id_obat']) ? (int) $_GET['id_obat'] : 0;
    $query = mysqli_query($conn, "SELECT * FROM obat WHERE id_obat=" . $id);
    if (!$query) {
        die("Gagal mengambil data obat: " . mysqli_error($conn));
    }
    $data = mysqli_fetch_array($query);

    if (!$data) {
        die('Data obat tidak ditemukan. <a href="obat_read.php">Kembali ke daftar obat</a>');
    }

    if (isset($_POST['simpan'])) {
        $kode_obat = $_POST['kode_obat'];
        $nama_obat = $_POST['nama_obat'];
        

        $update_query = "UPDATE obat SET kode_obat='$kode_obat', nama_obat='$nama_obat' WHERE id_obat=" . $id;
        if (!mysqli_query($conn, $update_query)) {
            die("Gagal menyimpan perubahan: " . mysqli_error($conn));
        }

        header("Location: obat_read.php");
        exit;
    }
?>

<html>
    <head></head>
    <body>
        <form method='POST' action='obat_edit.php?id_obat=<?php echo $id; ?>'>
            <table>
                <tr>
                    <td>Kode Obat</td>
                </tr>
                <tr>
                    <td><input type='text' name='kode_obat' value='<?php echo $data['kode_obat']; ?>'></td>
                </tr>
                <tr>
                    <td>Nama Obat</td>
                </tr>
                <tr>
                    <td><input type='text' name='nama_obat' value='<?php echo $data['nama_obat']; ?>'></td>
                </tr>
                 <tr>
                    <td><button type='submit' name='simpan'>Simpan</button></td>
                </tr>
            </table>
        </form>
    </body>
</html>