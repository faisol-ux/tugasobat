<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Strict',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

function is_admin_logged_in()
{
    return isset($_SESSION['admin_id'], $_SESSION['admin_username'])
        && isset($_SESSION['role'])
        && $_SESSION['role'] === 'admin';
}

function require_admin()
{
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}