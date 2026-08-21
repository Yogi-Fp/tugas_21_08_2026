<?php $title = 'Obat & Stok';
require_once __DIR__ . '/../includes/header.php';
$q = trim($_GET['q'] ?? '');
$s = db()->prepare('SELECT * FROM obat WHERE nama LIKE ? OR kode LIKE ? ORDER BY nama');
$s->execute(["%$q%", "%$q%"]);
$rows = $s->fetchAll(); ?>
<section class="panel">
    <div class="row">
        <h2>Persediaan Obat</h2>
        <div class="actions">
            <form><input name="q" value="<?= e($q) ?>" placeholder="Cari obat"></form>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?><a class="btn" href="form.php">+ Tambah
                    Obat</a><?php endif; ?>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>KODE</th>
                <th>NAMA OBAT</th>
                <th>SATUAN</th>
                <th>STOK</th>
                <th>HARGA</th><?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <th>AKSI</th><?php endif; ?>
            </tr>
        </thead>
        <tbody><?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= e($r['kode']) ?></td>
                    <td><b><?= e($r['nama']) ?></b></td>
                    <td><?= e($r['satuan']) ?></td>
                    <td><span class="badge <?= $r['stok'] < 10 ? 'red' : 'done' ?>"><?= e($r['stok']) ?></span></td>
                    <td><?= rupiah($r['harga']) ?></td><?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <td class="actions"><a class="btn secondary" href="form.php?id=<?= $r['id'] ?>">Ubah</a><a
                                class="btn danger" data-confirm="Hapus obat ini?" href="hapus.php?id=<?= $r['id'] ?>">Hapus</a>
                        </td><?php endif; ?>
                </tr><?php endforeach; ?>
        </tbody>
    </table>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>