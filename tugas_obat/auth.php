<?php
/**
 * Middleware Autentikasi Multi-Role: Dokter, Admin, dan Pasien
 * Project: SIMRS (Sistem Informasi Rumah Sakit)
 */
require_once __DIR__ . '/koneksi.php';

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function check_auth() {
    if (!is_logged_in()) {
        set_flash('warning', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        header("Location: login.php");
        exit;
    }
}

function current_user() {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id'          => $_SESSION['user_id'],
        'role'        => $_SESSION['role'] ?? 'dokter', // 'admin', 'dokter', 'pasien'
        'username'    => $_SESSION['username'] ?? '',
        'nama_user'   => $_SESSION['nama_user'] ?? $_SESSION['nama_dokter'] ?? 'Pengguna',
        'nama_dokter' => $_SESSION['nama_dokter'] ?? ($_SESSION['nama_user'] ?? 'Dokter'),
        'spesialis'   => $_SESSION['spesialis'] ?? '',
        'jenis'       => $_SESSION['jenis'] ?? ''
    ];
}

function is_admin() {
    return is_logged_in() && (($_SESSION['role'] ?? '') === 'admin');
}

function is_dokter() {
    return is_logged_in() && (($_SESSION['role'] ?? '') === 'dokter');
}

function is_pasien() {
    return is_logged_in() && (($_SESSION['role'] ?? '') === 'pasien');
}

/**
 * Hanya Dokter dan Admin yang boleh mengakses halaman/aksi tertentu
 * Pasien dilarang dan otomatis dialihkan ke Dashboard dengan pesan peringatan
 */
function require_staff() {
    check_auth();
    if (is_pasien()) {
        set_flash('danger', 'Akses dibatasi! Menu Obat, Resep, dan Pemeriksaan hanya dapat diakses oleh Dokter dan Admin.');
        header("Location: index.php");
        exit;
    }
}
?>
