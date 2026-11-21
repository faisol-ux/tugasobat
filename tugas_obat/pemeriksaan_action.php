<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';
check_auth();
require_staff();

$aksi = $_GET['aksi'] ?? '';

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pasien   = (int)$_POST['id_pasien'];
    $id_dokter   = (int)$_POST['id_dokter'];
    $tgl_periksa = clean($_POST['tgl_periksa']);
    $keluhan     = clean($_POST['keluhan']);
    $tensi_darah = clean($_POST['tensi_darah']);
    $obat_ids    = $_POST['obat_ids'] ?? [];

    if (empty($id_pasien) || empty($id_dokter) || empty($tgl_periksa)) {
        set_flash('danger', 'Data pasien, dokter, dan tanggal pemeriksaan wajib diisi.');
        header("Location: pemeriksaan_tambah.php");
        exit;
    }

    // 1. Simpan ke tabel pemeriksaan
    $query_periksa = "
        INSERT INTO pemeriksaan (id_pasien, id_dokter, tgl_periksa, keluhan, tensi_darah)
        VALUES ($id_pasien, $id_dokter, '$tgl_periksa', '$keluhan', '$tensi_darah')
    ";
    $simpan = mysqli_query($koneksi, $query_periksa);

    if ($simpan) {
        $id_pemeriksaan = mysqli_insert_id($koneksi);

        // Update keluhan & tensi di tabel pasien agar selalu sinkron
        mysqli_query($koneksi, "UPDATE pasien SET keluhan = '$keluhan', tensi_darah = '$tensi_darah' WHERE id = $id_pasien");

        // 2. Simpan obat yang dipilih ke tabel pemeriksaan_obat (kolom: id_pemeriksaan, id_id_obat)
        if (!empty($obat_ids) && is_array($obat_ids)) {
            foreach ($obat_ids as $id_obat) {
                $id_obat_clean = (int)$id_obat;
                mysqli_query($koneksi, "
                    INSERT INTO pemeriksaan_obat (id_pemeriksaan, id_id_obat) 
                    VALUES ($id_pemeriksaan, $id_obat_clean)
                ");
            }
        }

        set_flash('success', "Pemeriksaan pasien berhasil disimpan beserta " . count($obat_ids) . " resep obat.");
        header("Location: pemeriksaan_detail.php?id=" . $id_pemeriksaan);
        exit;
    } else {
        set_flash('danger', "Gagal menyimpan pemeriksaan: " . mysqli_error($koneksi));
        header("Location: pemeriksaan_tambah.php");
        exit;
    }
}

if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)$_POST['id'];
    $id_pasien   = (int)$_POST['id_pasien'];
    $id_dokter   = (int)$_POST['id_dokter'];
    $tgl_periksa = clean($_POST['tgl_periksa']);
    $keluhan     = clean($_POST['keluhan']);
    $tensi_darah = clean($_POST['tensi_darah']);
    $obat_ids    = $_POST['obat_ids'] ?? [];

    // 1. Update tabel pemeriksaan
    $query_update = "
        UPDATE pemeriksaan 
        SET id_pasien = $id_pasien,
            id_dokter = $id_dokter,
            tgl_periksa = '$tgl_periksa',
            keluhan = '$keluhan',
            tensi_darah = '$tensi_darah'
        WHERE id = $id
    ";
    $update = mysqli_query($koneksi, $query_update);

    if ($update) {
        // Update keluhan & tensi di tabel pasien
        mysqli_query($koneksi, "UPDATE pasien SET keluhan = '$keluhan', tensi_darah = '$tensi_darah' WHERE id = $id_pasien");

        // 2. Refresh obat di tabel pemeriksaan_obat
        // Hapus obat lama terkait pemeriksaan ini
        mysqli_query($koneksi, "DELETE FROM pemeriksaan_obat WHERE id_pemeriksaan = $id");

        // Masukkan obat baru yang dicentang
        if (!empty($obat_ids) && is_array($obat_ids)) {
            foreach ($obat_ids as $id_obat) {
                $id_obat_clean = (int)$id_obat;
                mysqli_query($koneksi, "
                    INSERT INTO pemeriksaan_obat (id_pemeriksaan, id_id_obat) 
                    VALUES ($id, $id_obat_clean)
                ");
            }
        }

        set_flash('success', "Data rekam pemeriksaan #$id berhasil diperbarui.");
        header("Location: pemeriksaan_detail.php?id=" . $id);
        exit;
    } else {
        set_flash('danger', "Gagal memperbarui pemeriksaan: " . mysqli_error($koneksi));
        header("Location: pemeriksaan_edit.php?id=" . $id);
        exit;
    }
}

if ($aksi === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);

    // Hapus data pemeriksaan_obat terlebih dahulu
    mysqli_query($koneksi, "DELETE FROM pemeriksaan_obat WHERE id_pemeriksaan = $id");

    // Hapus data pemeriksaan
    $delete = mysqli_query($koneksi, "DELETE FROM pemeriksaan WHERE id = $id");
    if ($delete) {
        set_flash('success', "Rekam pemeriksaan berhasil dihapus.");
    } else {
        set_flash('danger', "Gagal menghapus rekam pemeriksaan: " . mysqli_error($koneksi));
    }
    header("Location: pemeriksaan.php");
    exit;
}

header("Location: pemeriksaan.php");
exit;
?>

