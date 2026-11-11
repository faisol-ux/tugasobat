<?php
require_once 'admin_auth.php';
include 'koneksi.php';

if (is_admin_logged_in()) {
    header('Location: daftar.php');
    exit;
}

$tableCheck = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'admin_users'");
$adminTableExists = $tableCheck && (int) mysqli_fetch_assoc($tableCheck)['total'] > 0;
$setupRequired = !$adminTableExists;
$error = '';

if ($adminTableExists) {
    $countResult = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM admin_users");
    $setupRequired = !$countResult || (int) mysqli_fetch_assoc($countResult)['total'] === 0;
}

if (empty($_SESSION['login_csrf'])) {
    $_SESSION['login_csrf'] = bin2hex(random_bytes(32));
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['login_csrf'], $token)) {
        $error = 'Permintaan tidak valid. Muat ulang halaman dan coba lagi.';
    } elseif (!$adminTableExists) {
        $error = 'Admin belum disiapkan. Buka halaman setup dari komputer lokal.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $statement = mysqli_prepare($koneksi, 'SELECT id_admin, username, password_hash FROM admin_users WHERE username = ? LIMIT 1');
        mysqli_stmt_bind_param($statement, 's', $username);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $admin = mysqli_fetch_assoc($result);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['role'] = 'admin';
            unset($_SESSION['login_csrf']);
            header('Location: daftar.php');
            exit;
        }

        $error = 'Username atau password salah.';
        mysqli_stmt_close($statement);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin Pemeriksaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg-dark: #0f172a; --bg-card: #1e293b; --teal: #d83bcb; --teal-hover: #ac3078; --text-light: #f8fafc; --text-muted: #94a3b8; --border-color: #334155; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { min-height: 100vh; display: grid; place-items: center; padding: 24px; background: var(--bg-dark); color: var(--text-light); }
        .login-panel { width: min(100%, 420px); padding: 32px; border: 1px solid var(--border-color); border-radius: 14px; background: var(--bg-card); }
        .login-panel h1 { margin-bottom: 8px; font-size: 22px; }
        .login-panel > p { margin-bottom: 24px; color: var(--text-muted); font-size: 14px; }
        label { display: block; margin: 16px 0 8px; color: var(--text-muted); font-size: 13px; font-weight: 600; }
        input { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; outline: none; background: var(--bg-dark); color: var(--text-light); }
        input:focus { border-color: var(--teal); }
        button { width: 100%; margin-top: 22px; padding: 13px; border: 0; border-radius: 8px; background: var(--teal); color: white; font-size: 14px; font-weight: 700; cursor: pointer; }
        button:hover { background: var(--teal-hover); }
        .error { margin: 14px 0; color: #fca5a5; font-size: 13px; }
        .setup-link { display: block; margin-top: 18px; color: var(--text-muted); text-align: center; font-size: 13px; }
    </style>
</head>
<body>
    <main class="login-panel">
        <h1>Login Admin</h1>
        <p>Masuk untuk mengelola data pemeriksaan.</p>
        <?php if ($error !== '') { ?><div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php } ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['login_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" autocomplete="username" required>
            <label for="password">Password</label>
            <input id="password" type="password" name="password" autocomplete="current-password" required>
            <button type="submit"><i class="fas fa-right-to-bracket"></i> Masuk</button>
        </form>
        <?php if ($setupRequired) { ?>
            <a class="setup-link" href="admin_setup.php">Buat admin pertama dari komputer lokal</a>
        <?php } ?>
    </main>
</body>
</html>