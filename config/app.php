<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();
date_default_timezone_set('Asia/Jakarta');
require_once __DIR__ . '/database.php';

function e($text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}
function redirect(string $path)
{
    header('Location: ' . $path);
    exit;
}
function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }
    $out = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $out;
}
function logged_in(): bool
{
    return isset($_SESSION['user']);
}
function require_login(): void
{
    if (!logged_in())
        redirect('/CRUDP/auth/login.php');
}
function require_admin(): void
{
    require_login();
    if ($_SESSION['user']['role'] !== 'admin') {
        flash('error', 'Akses hanya untuk administrator.');
        redirect('/CRUDP/index.php');
    }
}
function rupiah($number): string
{
    return 'Rp ' . number_format((float) $number, 0, ',', '.');
}
function require_staff(): void
{
    require_login();
    if ($_SESSION['user']['role'] === 'user')
        redirect('/CRUDP/user/index.php');
}
