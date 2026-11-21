<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';
check_auth();
require_staff();

$aksi = $_GET['aksi'] ?? '';

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = clean($_POST['kode_obat']);
    $nama = clean($_POST['nama_obat']);

    if (empty($kode) || empty($nama)) {
        set_flash('danger', 'Kode obat dan nama obat wajib diisi.');
    } else {
        $cek = mysqli_query($koneksi, "SELECT id FROM obat WHERE kode_obat='$kode'");
        if (mysqli_num_rows($cek) > 0) {
            set_flash('danger', "Kode obat '$kode' sudah terdaftar.");
        } else {
            $insert = mysqli_query($koneksi, "INSERT INTO obat (kode_obat, nama_obat) VALUES ('$kode', '$nama')");
            if ($insert) {
                set_flash('success', "Obat $nama berhasil ditambahkan.");
            } else {
                set_flash('danger', "Gagal menambah obat: " . mysqli_error($koneksi));
            }
        }
    }
    header("Location: obat.php");
    exit;
}

if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)$_POST['id'];
    $kode = clean($_POST['kode_obat']);
    $nama = clean($_POST['nama_obat']);

    $cek = mysqli_query($koneksi, "SELECT id FROM obat WHERE kode_obat='$kode' AND id != $id");
    if (mysqli_num_rows($cek) > 0) {
        set_flash('danger', "Kode obat '$kode' sudah digunakan oleh obat lain.");
    } else {
        $update = mysqli_query($koneksi, "UPDATE obat SET kode_obat='$kode', nama_obat='$nama' WHERE id=$id");
        if ($update) {
            set_flash('success', "Data obat $nama berhasil diperbarui.");
        } else {
            set_flash('danger', "Gagal memperbarui obat: " . mysqli_error($koneksi));
        }
    }
    header("Location: obat.php");
    exit;
}

if ($aksi === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    $delete = mysqli_query($koneksi, "DELETE FROM obat WHERE id=$id");
    if ($delete) {
        set_flash('success', "Data obat berhasil dihapus.");
    } else {
        set_flash('danger', "Gagal menghapus obat: " . mysqli_error($koneksi));
    }
    header("Location: obat.php");
    exit;
}

header("Location: obat.php");
exit;
?>

