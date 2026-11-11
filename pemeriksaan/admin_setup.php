<?php
require_once 'admin_auth.php';
include 'koneksi.php';

$remoteAddress = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remoteAddress, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Setup admin hanya dapat dibuka dari komputer lokal.');
}

if (is_admin_logged_in()) {
    header('Location: daftar.php');
    exit;
}

$tableCreated = mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS admin_users (
    id_admin INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$error = $tableCreated ? '' : 'Tabel admin tidak dapat disiapkan. Periksa hak akses database.';
$adminExists = false;
if ($tableCreated) {
    $countResult = mysqli_query($koneksi, 'SELECT COUNT(*) AS total FROM admin_users');
    $adminExists = $countResult && (int) mysqli_fetch_assoc($countResult)['total'] > 0;
}

if (empty($_SESSION['setup_csrf'])) {
    $_SESSION['setup_csrf'] = bin2hex(random_bytes(32));
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && !$adminExists && $tableCreated) {
    $token = $_POST['csrf_token'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!hash_equals($_SESSION['setup_csrf'], $token)) {
        $error = 'Permintaan tidak valid. Muat ulang halaman dan coba lagi.';
    } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) {
        $error = 'Username harus 3-50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
    } elseif (strlen($password) < 12) {
        $error = 'Password harus memiliki minimal 12 karakter.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $statement = mysqli_prepare($koneksi, 'INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
        mysqli_stmt_bind_param($statement, 'ss', $username, $passwordHash);

        if (mysqli_stmt_execute($statement)) {
            unset($_SESSION['setup_csrf']);
            header('Location: login.php?setup=success');
            exit;
        }

        $error = 'Admin gagal dibuat. Pastikan username belum digunakan.';
        mysqli_stmt_close($statement);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-dark: #0f172a; --bg-card: #1e293b; --teal: #d83bcb; --text-light: #f8fafc; --text-muted: #94a3b8; --border-color: #334155; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { min-height: 100vh; display: grid; place-items: center; padding: 24px; background: var(--bg-dark); color: var(--text-light); }
        .setup-panel { width: min(100%, 460px); padding: 32px; border: 1px solid var(--border-color); border-radius: 14px; background: var(--bg-card); }
        h1 { margin-bottom: 8px; font-size: 22px; }
        p { margin-bottom: 20px; color: var(--text-muted); font-size: 14px; line-height: 1.6; }
        label { display: block; margin: 16px 0 8px; color: var(--text-muted); font-size: 13px; font-weight: 600; }
        input { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; outline: none; background: var(--bg-dark); color: var(--text-light); }
        input:focus { border-color: var(--teal); }
        button { width: 100%; margin-top: 22px; padding: 13px; border: 0; border-radius: 8px; background: var(--teal); color: white; font-size: 14px; font-weight: 700; cursor: pointer; }
        .error { margin-bottom: 14px; color: #fca5a5; font-size: 13px; }
        .back-link { display: block; margin-top: 18px; color: var(--text-muted); text-align: center; font-size: 13px; }
    </style>
</head>
<body>
    <main class="setup-panel">
        <h1>Setup Admin Pertama</h1>
        <?php if ($adminExists) { ?>
            <p>Akun admin sudah tersedia. Setup awal telah dikunci.</p>
            <a class="back-link" href="login.php">Ke halaman login</a>
        <?php } else { ?>
            <p>Buat akun admin. Gunakan password minimal 12 karakter.</p>
            <?php if ($error !== '') { ?><div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php } ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['setup_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" minlength="3" maxlength="50" autocomplete="username" required>
                <label for="password">Password</label>
                <input id="password" type="password" name="password" minlength="12" autocomplete="new-password" required>
                <label for="confirm_password">Konfirmasi Password</label>
                <input id="confirm_password" type="password" name="confirm_password" minlength="12" autocomplete="new-password" required>
                <button type="submit">Buat Akun Admin</button>
            </form>
        <?php } ?>
    </main>
</body>
</html>