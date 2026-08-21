<?php $title = 'Data Pasien';
require_once __DIR__ . '/../includes/header.php';
$q = trim($_GET['q'] ?? '');
$s = db()->prepare('SELECT id,no_rm,nama,jenis_kelamin FROM pasien WHERE nama LIKE ? OR no_rm LIKE ? ORDER BY id DESC');
$s->execute(["%$q%", "%$q%"]);
$rows = $s->fetchAll(); ?>
<section class="panel">
    <div class="row">
        <h2>Daftar Pasien</h2>
        <div class="actions">
            <form method="get"><input name="q" value="<?= e($q) ?>" placeholder="Cari nama / no. RM"></form><a class="btn"
                href="form.php">+ Tambah Pasien</a>
        </div>
    </div>
    <div class="table-wrap">
        <table class="patient-table">
            <thead>
                <tr>
                    <th>NO. RM</th>
                    <th>NAMA</th>
                    <th>JK</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody><?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= e($r['no_rm']) ?></td>
                        <td><b><?= e($r['nama']) ?></b></td>
                        <td><?= e($r['jenis_kelamin']) ?></td>
                        <td>
                            <div class="actions action-inline"><a class="btn secondary"
                                    href="form.php?id=<?= $r['id'] ?>">Ubah</a><a class="btn danger"
                                    data-confirm="Hapus data pasien ini?" href="hapus.php?id=<?= $r['id'] ?>">Hapus</a></div>
                        </td>
                    </tr><?php endforeach; ?><?php if (!$rows): ?>
                    <tr>
                        <td colspan="4" class="muted">Data pasien tidak ditemukan.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>