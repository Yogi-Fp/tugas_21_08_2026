<?php $title = 'Pembayaran & Laporan';
require_once __DIR__ . '/../includes/header.php';
$rows = db()->query('SELECT b.*,p.nama pasien,k.no_kunjungan FROM pembayaran b JOIN pemeriksaan k ON k.id=b.pemeriksaan_id JOIN pasien p ON p.id=k.pasien_id ORDER BY b.id DESC')->fetchAll(); ?>
<section class="panel">
    <div class="row">
        <h2>Riwayat Pembayaran</h2><a class="btn" href="/pemeriksaan/index.php">+ Proses Pembayaran</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>INVOICE</th>
                <th>PASIEN</th>
                <th>TANGGAL</th>
                <th>METODE</th>
                <th>TOTAL</th>
                <th>STRUK</th>
            </tr>
        </thead>
        <tbody><?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= e($r['no_invoice']) ?><br><small class="muted"><?= e($r['no_kunjungan']) ?></small></td>
                    <td><?= e($r['pasien']) ?></td>
                    <td><?= date('d M Y H:i', strtotime($r['created_at'])) ?></td>
                    <td><?= e($r['metode']) ?></td>
                    <td><b><?= rupiah($r['total']) ?></b></td>
                    <td><a class="btn secondary" href="struk.php?id=<?= $r['id'] ?>">Cetak</a></td>
                </tr><?php endforeach; ?><?php if (!$rows): ?>
                <tr>
                    <td colspan="6" class="muted">Belum ada pembayaran.</td>
                </tr><?php endif; ?>
        </tbody>
    </table>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>