<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';
check_auth();
require_staff();

$aksi = $_GET['aksi'] ?? '';

// 1. TAMBAH RESEP OBAT
if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pemeriksaan = (int)($_POST['id_pemeriksaan'] ?? 0);
    $id_id_obat     = (int)($_POST['id_id_obat'] ?? 0);
    $aturan_pakai   = clean($_POST['aturan_pakai'] ?? '3x1 Sehari Sesudah Makan');
    if (empty($aturan_pakai)) {
        $aturan_pakai = '3x1 Sehari Sesudah Makan';
    }

    if ($id_pemeriksaan <= 0 || $id_id_obat <= 0) {
        set_flash('danger', 'Rekam pemeriksaan dan jenis obat wajib dipilih.');
    } else {
        $insert = mysqli_query($koneksi, "
            INSERT INTO pemeriksaan_obat (id_pemeriksaan, id_id_obat, aturan_pakai) 
            VALUES ($id_pemeriksaan, $id_id_obat, '$aturan_pakai')
        ");
        if ($insert) {
            set_flash('success', "Resep obat berhasil ditambahkan dengan aturan pakai: '$aturan_pakai'.");
        } else {
            set_flash('danger', 'Gagal menambahkan resep obat: ' . mysqli_error($koneksi));
        }
    }
    header("Location: resep.php");
    exit;
}

// 2. EDIT / UPDATE RESEP OBAT (EDIT TEXT ATURAN PAKAI & OBAT)
if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id             = (int)($_POST['id'] ?? 0);
    $id_pemeriksaan = (int)($_POST['id_pemeriksaan'] ?? 0);
    $id_id_obat     = (int)($_POST['id_id_obat'] ?? 0);
    $aturan_pakai   = clean($_POST['aturan_pakai'] ?? '');
    if (empty($aturan_pakai)) {
        $aturan_pakai = '3x1 Sehari Sesudah Makan';
    }

    if ($id <= 0 || $id_pemeriksaan <= 0 || $id_id_obat <= 0) {
        set_flash('danger', 'Data resep tidak valid atau belum lengkap.');
    } else {
        $update = mysqli_query($koneksi, "
            UPDATE pemeriksaan_obat 
            SET id_pemeriksaan = $id_pemeriksaan, 
                id_id_obat = $id_id_obat, 
                aturan_pakai = '$aturan_pakai' 
            WHERE id = $id
        ");
        if ($update) {
            set_flash('success', "Data resep obat #$id berhasil diperbarui (Aturan pakai: '$aturan_pakai').");
        } else {
            set_flash('danger', 'Gagal memperbarui resep: ' . mysqli_error($koneksi));
        }
    }
    header("Location: resep.php");
    exit;
}

// 3. QUICK EDIT TEXT (UPDATE CEPAT ATURAN PAKAI LANGSUNG DARI TABEL)
if ($aksi === 'quick_text' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = (int)($_POST['id'] ?? 0);
    $aturan_pakai = clean($_POST['aturan_pakai'] ?? '');
    if (empty($aturan_pakai)) {
        $aturan_pakai = '3x1 Sehari Sesudah Makan';
    }

    if ($id > 0) {
        $update = mysqli_query($koneksi, "UPDATE pemeriksaan_obat SET aturan_pakai = '$aturan_pakai' WHERE id = $id");
        if ($update) {
            set_flash('success', "Aturan pakai resep #$id berhasil diperbarui menjadi '$aturan_pakai'.");
        } else {
            set_flash('danger', 'Gagal memperbarui aturan pakai: ' . mysqli_error($koneksi));
        }
    }
    header("Location: resep.php");
    exit;
}

// 4. HAPUS ITEM RESEP OBAT
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
