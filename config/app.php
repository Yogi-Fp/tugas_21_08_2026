
<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

session_start();

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/database.php';

/**
 * Escape output HTML
 */
function e($text): string
{
    return htmlspecialchars(
        (string) $text,
        ENT_QUOTES,
        'UTF-8'
    );
}

/**
 * Redirect ke halaman tertentu
 */
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/**
 * Flash message
 */
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

/**
 * Mengecek apakah user sudah login
 */
function logged_in(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Memastikan user sudah login
 */
function require_login(): void
{
    if (!logged_in()) {
        redirect('/auth/login.php');
    }
}

/**
 * Memastikan user adalah admin
 */
function require_admin(): void
{
    require_login();

    if (
        !isset($_SESSION['user']['role']) ||
        $_SESSION['user']['role'] !== 'admin'
    ) {
        flash(
            'error',
            'Akses hanya untuk administrator.'
        );

        redirect('/index.php');
    }
}

/**
 * Format angka menjadi Rupiah
 */
function rupiah($number): string
{
    return 'Rp ' . number_format(
        (float) $number,
        0,
        ',',
        '.'
    );
}

/**
 * Memastikan user adalah staff/admin
 * User biasa akan diarahkan ke dashboard user
 */
function require_staff(): void
{
    require_login();

    if (
        isset($_SESSION['user']['role']) &&
        $_SESSION['user']['role'] === 'user'
    ) {
        redirect('/user/index.php');
    }
}
