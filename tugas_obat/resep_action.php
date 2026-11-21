<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';
check_auth();
require_staff();

$aksi = $_GET['aksi'] ?? '';

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pemeriksaan = (int)$_POST['id_pemeriksaan'];
    $id_id_obat     = (int)$_POST['id_id_obat'];

    if ($id_pemeriksaan <= 0 || $id_id_obat <= 0) {
        set_flash('danger', 'Pemeriksaan dan obat wajib dipilih.');
    } else {
        $insert = mysqli_query($koneksi, "
            INSERT INTO pemeriksaan_obat (id_pemeriksaan, id_id_obat) 
            VALUES ($id_pemeriksaan, $id_id_obat)
        ");
        if ($insert) {
            set_flash('success', 'Obat berhasil ditambahkan ke rekam pemeriksaan.');
        } else {
            set_flash('danger', 'Gagal menambahkan obat: ' . mysqli_error($koneksi));
        }
    }
    header("Location: resep.php");
    exit;
}

if ($aksi === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    $delete = mysqli_query($koneksi, "DELETE FROM pemeriksaan_obat WHERE id = $id");
    if ($delete) {
        set_flash('success', 'Item resep obat berhasil dihapus.');
    } else {
        set_flash('danger', 'Gagal menghapus item resep obat: ' . mysqli_error($koneksi));
    }
    header("Location: resep.php");
    exit;
}

header("Location: resep.php");
exit;
?>

