<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/auth.php';
check_auth();
require_staff();

$aksi = $_GET['aksi'] ?? '';

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username']);
    $password = clean($_POST['password']);
    $nama     = clean($_POST['nama_dokter']);
    $alamat   = clean($_POST['alamat']);
    $tgl      = clean($_POST['tgl_lahir']);
    $jenis    = clean($_POST['jenis']);
    $spesialis= clean($_POST['spesialis']);

    // Cek username unik
    $cek = mysqli_query($koneksi, "SELECT id FROM dokter WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        set_flash('danger', "Username '$username' sudah digunakan dokter lain.");
    } else {
        $insert = mysqli_query($koneksi, "
            INSERT INTO dokter (username, password, nama_dokter, alamat, tgl_lahir, jenis, spesialis) 
            VALUES ('$username', '$password', '$nama', '$alamat', '$tgl', '$jenis', '$spesialis')
        ");
        if ($insert) {
            set_flash('success', "Dokter $nama berhasil ditambahkan.");
        } else {
            set_flash('danger', "Gagal menambah dokter: " . mysqli_error($koneksi));
        }
    }
    header("Location: dokter.php");
    exit;
}

if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)$_POST['id'];
    $username  = clean($_POST['username']);
    $password  = clean($_POST['password']);
    $nama      = clean($_POST['nama_dokter']);
    $alamat    = clean($_POST['alamat']);
    $tgl       = clean($_POST['tgl_lahir']);
    $jenis     = clean($_POST['jenis']);
    $spesialis = clean($_POST['spesialis']);

    // Cek duplikasi username untuk dokter lain
    $cek = mysqli_query($koneksi, "SELECT id FROM dokter WHERE username='$username' AND id != $id");
    if (mysqli_num_rows($cek) > 0) {
        set_flash('danger', "Username '$username' sudah terdaftar.");
    } else {
        if (!empty($password)) {
            $sql = "UPDATE dokter SET username='$username', password='$password', nama_dokter='$nama', alamat='$alamat', tgl_lahir='$tgl', jenis='$jenis', spesialis='$spesialis' WHERE id=$id";
        } else {
            $sql = "UPDATE dokter SET username='$username', nama_dokter='$nama', alamat='$alamat', tgl_lahir='$tgl', jenis='$jenis', spesialis='$spesialis' WHERE id=$id";
        }
        $update = mysqli_query($koneksi, $sql);
        if ($update) {
            // Perbarui session jika dokter yang sedang login mengedit profilnya sendiri
            if ($_SESSION['user_id'] == $id) {
                $_SESSION['username']    = $username;
                $_SESSION['nama_dokter'] = $nama;
                $_SESSION['spesialis']   = $spesialis;
                $_SESSION['jenis']       = $jenis;
            }
            set_flash('success', "Data dokter $nama berhasil diperbarui.");
        } else {
            set_flash('danger', "Gagal memperbarui dokter: " . mysqli_error($koneksi));
        }
    }
    header("Location: dokter.php");
    exit;
}

if ($aksi === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    // Jangan izinkan dokter menghapus dirinya sendiri yang sedang login
    if ($id == $_SESSION['user_id']) {
        set_flash('warning', "Anda tidak dapat menghapus akun Anda sendiri saat sedang login.");
    } else {
        $delete = mysqli_query($koneksi, "DELETE FROM dokter WHERE id=$id");
        if ($delete) {
            set_flash('success', "Data dokter berhasil dihapus.");
        } else {
            set_flash('danger', "Gagal menghapus dokter: " . mysqli_error($koneksi));
        }
    }
    header("Location: dokter.php");
    exit;
}

header("Location: dokter.php");
exit;
?>

