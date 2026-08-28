<?php $title = 'Pemeriksaan Pasien';
require_once __DIR__ . '/../includes/header.php';
$rows = db()->query('SELECT k.*,p.nama pasien,d.nama dokter FROM pemeriksaan k JOIN pasien p ON p.id=k.pasien_id JOIN dokter d ON d.id=k.dokter_id ORDER BY k.id DESC')->fetchAll(); ?>
<section class="panel">
    <div class="row">
        <h2>Daftar Kunjungan</h2><a class="btn" href="form.php">+ Pendaftaran Baru</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>NO. KUNJUNGAN</th>
                <th>PASIEN</th>
                <th>DOKTER</th>
                <th>KELUHAN</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody><?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= e($r['no_kunjungan']) ?><br><small
                            class="muted"><?= date('d/m/Y H:i', strtotime($r['tanggal'])) ?></small></td>
                    <td><b><?= e($r['pasien']) ?></b></td>
                    <td><?= e($r['dokter']) ?></td>
                    <td><?= e($r['keluhan']) ?></td>
                    <td><span class="badge <?= $r['status'] === 'dibayar' ? 'done' : '' ?>"><?= e(ucfirst($r['status'])) ?></span>
                    </td>
                    <td class="actions"><a class="btn secondary"
                            href="form.php?id=<?= $r['id'] ?>">Detail</a><?php if ($r['status'] !== 'dibayar'): ?><a
                                class="btn warning" href="/pembayaran/form.php?id=<?= $r['id'] ?>">Bayar</a><?php endif; ?>
                    </td>
                </tr><?php endforeach; ?><?php if (!$rows): ?>
                <tr>
                    <td colspan="6" class="muted">Belum ada pendaftaran.</td>
                </tr><?php endif; ?>
        </tbody>
    </table>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>