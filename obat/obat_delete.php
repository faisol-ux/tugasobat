<?php
    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");
    $id = isset($_GET['id_obat']) ? (int) $_GET['id_obat'] : (isset($_GET['id']) ? (int) $_GET['id'] : 0);
    $berhasil = false;
    $pesan_gagal = "Data obat tidak ditemukan.";
    if ($conn) {
        if ($id > 0) {
            $query = mysqli_query($conn, "DELETE FROM obat WHERE id_obat=" . $id);
            $berhasil = $query && mysqli_affected_rows($conn) > 0;
            if (!$query) {
                $pesan_gagal = "Gagal menghapus data obat: " . mysqli_error($conn);
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
        <title>Hapus Obat</title>
    </head>
    <body>
        <?php if ($berhasil) { ?>
            <p>Data obat berhasil dihapus.</p>
        <?php } else { ?>
            <p><?php echo htmlspecialchars($pesan_gagal, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php } ?>
        <a href="obat_read.php"><button type="button">Kembali ke Obat Read</button></a>
    </body>
</html>
