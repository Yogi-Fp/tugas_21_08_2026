<?php $title = 'Manajemen Pengguna';
require_once __DIR__ . '/config/app.php';
require_admin();
require_once __DIR__ . '/includes/header.php';
$rows = db()->query('SELECT id,nama,username,role,created_at FROM users ORDER BY id')->fetchAll(); ?>
<section class="panel">
    <h2>Akun Pengguna</h2>
    <p class="muted">Akun bawaan: admin dan petugas, keduanya menggunakan password <b>password</b>. Admin memiliki akses
        untuk mengelola obat dan pengguna.</p>
    <table>
        <thead>
            <tr>
                <th>NAMA</th>
                <th>USERNAME</th>
                <th>ROLE</th>
                <th>DIBUAT</th>
            </tr>
        </thead>
        <tbody><?php foreach ($rows as $u): ?>
                <tr>
                    <td><?= e($u['nama']) ?></td>
                    <td><?= e($u['username']) ?></td>
                    <td><span class="badge done"><?= e($u['role']) ?></span></td>
                    <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                </tr><?php endforeach; ?>
        </tbody>
    </table>
</section><?php require __DIR__ . '/includes/footer.php'; ?>