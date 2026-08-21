<?php
require_once __DIR__ . '/../config/app.php';
if (logged_in())
    redirect('/CRUDP/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $s = db()->prepare('SELECT id,nama,username,password,role FROM users WHERE username = ? LIMIT 1');
    $s->execute([$username]);
    $user = $s->fetch();
    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        redirect('/CRUDP/index.php');
    }
    $error = 'Username atau password tidak sesuai.';
}
?><!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Masuk · SehatCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="/CRUDP/assets/css/style.css">
</head>

<body class="login">
    <section class="login-box">
        <p class="eyebrow">SISTEM INFORMASI KLINIK</p>
        <h1><i class="fa-solid fa-heart-pulse" style="color:#15755e"></i> SehatCare</h1>
        <p class="muted">Masuk untuk mengelola layanan klinik.</p><?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form method="post"><label>Username<input name="username" required autofocus></label><label>Password<input
                    type="password" name="password" required></label><button class="btn">Masuk ke Sistem →</button>
        </form>
        <p class="muted">Belum punya akun? <a href="register.php">Buat akun </a></p>
        <p class="muted">Demo: admin / password &nbsp;•&nbsp; petugas / password</p>
    </section>
</body>

</html>