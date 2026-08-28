<?php require_once __DIR__ . '/../config/app.php';
session_unset();
session_destroy();
header('Location: /auth/login.php');
exit;
