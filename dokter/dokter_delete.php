<?php
    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");
    $id = isset($_GET['id_dokter']) ? (int) $_GET['id_dokter'] : (isset($_GET['id']) ? (int) $_GET['id'] : 0);
    $berhasil = false;
    $pesan_gagal = "Data Dokter tidak ditemukan.";
    if ($conn) {
        if ($id > 0) {
            $query = mysqli_query($conn, "DELETE FROM dokter WHERE id_dokter=" . $id);
            $berhasil = $query && mysqli_affected_rows($conn) > 0;
            if (!$query) {
                $pesan_gagal = "Gagal menghapus data dokter: " . mysqli_error($conn);
            }
        }
    } else {
        $pesan_gagal = "Koneksi database gagal: " . mysqli_connect_error();
    }
?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Hapus Dokter</title>
    </head>
    <body>
        <?php if ($berhasil) { ?>
            <p>Data Dokter berhasil dihapus.</p>
        <?php } else { ?>
            <p><?php echo htmlspecialchars($pesan_gagal, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php } ?>
        <a href="dokter_read.php"><button type="button">Kembali ke Dokter Read</button></a>
    </body>
</html>
