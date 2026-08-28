<?php require_once __DIR__ . '/../config/app.php';
require_login();
$section = basename(dirname($_SERVER['SCRIPT_NAME']));
if ($_SESSION['user']['role'] === 'user' && !in_array($section, ['user', 'pembayaran'], true))
    redirect('/user/index.php');
$title = $title ?? 'SehatCare'; ?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title) ?> · SehatCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <aside class="sidebar"><a class="brand" href="/index.php"><span><i class="fa-solid fa-heart-pulse"></i></span>
            SehatCare<small>KLINIK MANAGEMENT</small></a>
        <nav>
            <?php if ($_SESSION['user']['role'] === 'user'): ?><a href="/user/index.php"><i
                        class="fa-solid fa-house"></i> Portal Saya</a><a href="/user/booking.php"><i
                        class="fa-solid fa-calendar-plus"></i> Buat Janji</a><?php else: ?><a href="/index.php">▦
                    Dashboard</a><a href="/pasien/index.php">♙ Data Pasien</a><a href="/obat/index.php">▣ Obat &
                    Stok</a><a href="/pemeriksaan/index.php">✚ Pemeriksaan</a><a href="/pembayaran/index.php">▤
                    Pembayaran</a><?php endif; ?>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?><a href="/users.php">♧ Pengguna</a><?php endif; ?>
        </nav>
        <div class="profile">
            <b><?= e($_SESSION['user']['nama']) ?></b><small><?= e(ucfirst($_SESSION['user']['role'])) ?></small><a
                href="/auth/logout.php">Keluar →</a></div>
    </aside>
    <main>
        <header>
            <div>
                <p class="eyebrow">SISTEM INFORMASI KLINIK</p>
                <h1><?= e($title) ?></h1>
            </div>
            <div class="date"><?= date('l, d F Y') ?></div>
        </header>
        <?php if ($msg = flash('success')): ?>
            <div class="alert success"><?= e($msg) ?></div><?php endif; ?><?php if ($msg = flash('error')): ?>
            <div class="alert error"><?= e($msg) ?></div><?php endif; ?>