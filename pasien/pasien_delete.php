<?php
    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $berhasil = false;
    if ($conn) {
        $query = mysqli_query($conn, "DELETE FROM pasien WHERE id_pasien=" . $id);
        $berhasil = $query && mysqli_affected_rows($conn) > 0;
    }
?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Hapus Pasien</title>
    </head>
    <body>
        <?php if ($berhasil) { ?>
            <p>Data pasien berhasil dihapus.</p>
        <?php } else { ?>
            <p>Data pasien tidak ditemukan atau gagal dihapus.</p>
        <?php } ?>
        <a href="pasien_read.php"><button type="button">Kembali ke Pasien Read</button></a>
    </body>
</html>
