<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_OFF);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "rumah sakit";

$koneksi = @mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    $koneksi = @mysqli_connect($host, $user, $pass);
    if (!$koneksi) {
        die("
        <div style='font-family: sans-serif; max-width: 650px; margin: 60px auto; padding: 25px; border-radius: 12px; background: #fee2e2; border: 1px solid #ef4444; color: #991b1b;'>
            <h2 style='margin-top:0;'>⚠️ Gagal Terhubung ke MySQL</h2>
            <p>Pastikan modul <strong>MySQL di XAMPP Control Panel</strong> sudah dijalankan (Status: Running).</p>
            <p><small>Error: " . mysqli_connect_error() . "</small></p>
        </div>
        ");
    }

    mysqli_query($koneksi, "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    mysqli_select_db($koneksi, $db);

    $sql_file = __DIR__ . '/database.sql';
    if (file_exists($sql_file)) {
        $sql_commands = file_get_contents($sql_file);
        mysqli_multi_query($koneksi, $sql_commands);
        while (mysqli_more_results($koneksi) && mysqli_next_result($koneksi)) {
            if ($result = mysqli_store_result($koneksi)) {
                mysqli_free_result($result);
            }
        }
    }
}

mysqli_set_charset($koneksi, "utf8mb4");

function clean($str) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, trim($str ?? ''));
}

function tgl_indo($tanggal) {
    if (!$tanggal || $tanggal == '0000-00-00') return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecahkan = explode('-', $tanggal);
    if (count($pecahkan) !== 3) return $tanggal;
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, 
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>

