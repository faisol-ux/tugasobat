<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';
check_auth();

$aksi = $_GET['aksi'] ?? '';

// ===============================================
// 1. TAMBAH PASIEN (DAPAT DIAKSES PASIEN, DOKTER & ADMIN)
// Menyimpan data pasien sekaligus merekam keluhan & tensi
// ===============================================
if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_reg      = clean($_POST['no_registrasi'] ?? '');
    $nama        = clean($_POST['nama_pasien'] ?? '');
    $alamat      = clean($_POST['alamat'] ?? '');
    $tgl         = clean($_POST['tgl_lahir'] ?? '');
    $keluhan     = clean($_POST['keluhan'] ?? '');
    $tensi_darah = clean($_POST['tensi_darah'] ?? '');
    $id_dokter   = (int)($_POST['id_dokter'] ?? 0);

    // Auto default jika kosong
    if (empty($no_reg)) {
        $q_last = mysqli_query($koneksi, "SELECT id FROM pasien ORDER BY id DESC LIMIT 1");
        $last_id = ($r_last = mysqli_fetch_assoc($q_last)) ? $r_last['id'] + 1 : 1;
        $no_reg = 'REG-' . date('Y') . '-' . str_pad($last_id, 3, '0', STR_PAD_LEFT);
    }
    if (empty($tgl)) {
        $tgl = date('Y-m-d');
    }
    if (empty($alamat)) {
        $alamat = 'Surabaya';
    }

    // Jika dokter belum dipilih, ambil dokter pertama
    if ($id_dokter <= 0) {
        $q_dok = mysqli_query($koneksi, "SELECT id FROM dokter ORDER BY id ASC LIMIT 1");
        $id_dokter = ($r_dok = mysqli_fetch_assoc($q_dok)) ? (int)$r_dok['id'] : 1;
    }

    if (empty($nama)) {
        set_flash('danger', 'Nama pasien wajib diisi.');
    } else {
        // 1. Simpan ke tabel pasien (termasuk keluhan dan tensi sesuai wireframe)
        $insert = mysqli_query($koneksi, "
            INSERT INTO pasien (no_registrasi, nama_pasien, alamat, tgl_lahir, keluhan, tensi_darah) 
            VALUES ('$no_reg', '$nama', '$alamat', '$tgl', '$keluhan', '$tensi_darah')
        ");

        if ($insert) {
            $new_pasien_id = mysqli_insert_id($koneksi);

            // 2. Langsung buatkan entri pemeriksaan antrean dokter sehingga keluhan pasien sudah otomatis ada
            $tgl_periksa = date('Y-m-d');
            $keluhan_val = !empty($keluhan) ? $keluhan : 'Pemeriksaan kesehatan umum';
            $tensi_val   = !empty($tensi_darah) ? $tensi_darah : '120/80 mmHg';

            mysqli_query($koneksi, "
                INSERT INTO pemeriksaan (id_pasien, id_dokter, tgl_periksa, keluhan, tensi_darah) 
                VALUES ($new_pasien_id, $id_dokter, '$tgl_periksa', '$keluhan_val', '$tensi_val')
            ");

            set_flash('success', "Pendaftaran pasien '$nama' berhasil disimpan! Keluhan dan tensi darah tersimpan di profil pasien dan siap diperiksa dokter.");
        } else {
            set_flash('danger', "Gagal menambah data pasien: " . mysqli_error($koneksi));
        }
    }
    header("Location: pasien.php");
    exit;
}

// ===============================================
// 2. EDIT PASIEN (HANYA DOKTER & ADMIN)
// ===============================================
if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (is_pasien()) {
        set_flash('danger', 'Pasien tidak memiliki izin untuk mengedit data pasien lain.');
        header("Location: pasien.php");
        exit;
    }

    $id          = (int)$_POST['id'];
    $no_reg      = clean($_POST['no_registrasi']);
    $nama        = clean($_POST['nama_pasien']);
    $alamat      = clean($_POST['alamat']);
    $tgl         = clean($_POST['tgl_lahir']);
    $keluhan     = clean($_POST['keluhan'] ?? '');
    $tensi_darah = clean($_POST['tensi_darah'] ?? '');

    $update = mysqli_query($koneksi, "
        UPDATE pasien 
        SET no_registrasi='$no_reg', 
            nama_pasien='$nama', 
            alamat='$alamat', 
            tgl_lahir='$tgl',
            keluhan='$keluhan',
            tensi_darah='$tensi_darah'
        WHERE id=$id
    ");
    if ($update) {
        set_flash('success', "Data pasien $nama berhasil diperbarui.");
    } else {
        set_flash('danger', "Gagal memperbarui data pasien: " . mysqli_error($koneksi));
    }
    header("Location: pasien.php");
    exit;
}

// ===============================================
// 3. HAPUS PASIEN (HANYA DOKTER & ADMIN)
// ===============================================
if ($aksi === 'hapus') {
    if (is_pasien()) {
        set_flash('danger', 'Pasien tidak memiliki izin untuk menghapus data pasien.');
        header("Location: pasien.php");
        exit;
    }

    $id = (int)($_GET['id'] ?? 0);
    $delete = mysqli_query($koneksi, "DELETE FROM pasien WHERE id=$id");
    if ($delete) {
        set_flash('success', "Data pasien berhasil dihapus.");
    } else {
        set_flash('danger', "Gagal menghapus data pasien: " . mysqli_error($koneksi));
    }
    header("Location: pasien.php");
    exit;
}

header("Location: pasien.php");
exit;
?>
