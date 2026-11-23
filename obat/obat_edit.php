<?php
    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    $id = isset($_GET['id_obat']) ? (int) $_GET['id_obat'] : (int)($_GET['id'] ?? 0);
    $query = mysqli_query($conn, "SELECT * FROM obat WHERE id=" . $id);
    if (!$query || mysqli_num_rows($query) == 0) {
        $query = mysqli_query($conn, "SELECT * FROM obat WHERE id_obat=" . $id);
    }
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
        $frekuensi = isset($_POST['frekuensi_peresepan']) ? (int)$_POST['frekuensi_peresepan'] : 0;

        $update_query = "UPDATE obat SET kode_obat='$kode_obat', nama_obat='$nama_obat', frekuensi_peresepan=$frekuensi WHERE id=" . $id;
        if (!mysqli_query($conn, $update_query)) {
            $update_query = "UPDATE obat SET kode_obat='$kode_obat', nama_obat='$nama_obat', frekuensi_peresepan=$frekuensi WHERE id_obat=" . $id;
            mysqli_query($conn, $update_query);
        }

        header("Location: obat_read.php");
        exit;
    }
?>

<html>
    <head><title>Edit Obat</title></head>
    <body>
        <form method='POST' action='obat_edit.php?id_obat=<?php echo $id; ?>'>
            <table>
                <tr>
                    <td>Kode Obat</td>
                </tr>
                <tr>
                    <td><input type='text' name='kode_obat' value='<?php echo htmlspecialchars($data['kode_obat']); ?>'></td>
                </tr>
                <tr>
                    <td>Nama Obat</td>
                </tr>
                <tr>
                    <td><input type='text' name='nama_obat' value='<?php echo htmlspecialchars($data['nama_obat']); ?>'></td>
                </tr>
                <tr>
                    <td>Frekuensi Peresepan</td>
                </tr>
                <tr>
                    <td><input type='number' name='frekuensi_peresepan' value='<?php echo (int)($data['frekuensi_peresepan'] ?? 0); ?>' min='0'></td>
                </tr>
                <tr>
                    <td><button type='submit' name='simpan'>Simpan</button></td>
                </tr>
            </table>
        </form>
    </body>
</html>
