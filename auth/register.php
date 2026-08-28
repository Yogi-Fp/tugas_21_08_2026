<?php
require_once __DIR__ . '/../config/app.php';
if (logged_in())
    redirect('/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (strlen($nama) < 3 || strlen($username) < 3 || strlen($password) < 6)
        $error = 'Nama, username minimal 3 karakter dan password minimal 6 karakter.';
    elseif ($password !== $confirm)
        $error = 'Konfirmasi password tidak sama.';
    else
        try {
            $pdo = db();
            $pdo->beginTransaction();
            $s = $pdo->prepare('INSERT INTO users(nama,username,password,role) VALUES(?,?,?,?)');
            $s->execute([$nama, $username, password_hash($password, PASSWORD_DEFAULT), 'user']);
            $userId = (int) $pdo->lastInsertId();
            $no = 'RM-' . date('ymd') . '-' . str_pad((string) $userId, 4, '0', STR_PAD_LEFT);
            $pdo->prepare('INSERT INTO pasien(user_id,no_rm,nama,jenis_kelamin) VALUES(?,?,?,?)')->execute([$userId, $no, $nama, 'L']);
            $pdo->commit();
            flash('success', 'Akun pelanggan berhasil dibuat. Silakan masuk.');
            redirect('login.php');
        } catch (Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction())
                $pdo->rollBack();
            $error = $e->getCode() == 23000 ? 'Username sudah digunakan.' : 'Pendaftaran gagal: ' . $e->getMessage();
        }
}
?><!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Buat Akun · SehatCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body class="login">
    <section class="login-box">
        <p class="eyebrow">PENDAFTARAN PELANGGAN</p>
        <h1><i class="fa-solid fa-user-plus" style="color:#15755e"></i> Buat Akun</h1><?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form method="post"><label>Nama Lengkap<input name="nama" required
                    value="<?= e($_POST['nama'] ?? '') ?>"></label><label>Username<input name="username" required
                    value="<?= e($_POST['username'] ?? '') ?>"></label><label>Password<input type="password" name="password"
                    minlength="6" required></label><label>Ulangi Password<input type="password" name="confirm"
                    minlength="6" required></label><button class="btn">Daftar sebagai Pelanggan</button></form>
        <p class="muted"><a href="login.php">← Kembali ke login</a></p>
    </section>
</body>

</html>