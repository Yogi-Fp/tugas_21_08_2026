<?php $title = 'Struk Pembayaran';
require_once __DIR__ . '/../includes/header.php';
$s = db()->prepare('SELECT b.*,k.no_kunjungan,k.tanggal,p.nama pasien,p.no_rm,d.nama dokter FROM pembayaran b JOIN pemeriksaan k ON k.id=b.pemeriksaan_id JOIN pasien p ON p.id=k.pasien_id JOIN dokter d ON d.id=k.dokter_id WHERE b.id=?');
$s->execute([(int) $_GET['id']]);
$b = $s->fetch() ?: redirect('index.php');
$s = db()->prepare('SELECT po.*,o.nama FROM pemeriksaan_obat po JOIN obat o ON o.id=po.obat_id WHERE pemeriksaan_id=?');
$s->execute([$b['pemeriksaan_id']]);
$items = $s->fetchAll(); ?>
<section class="panel receipt">
    <div class="receipt-head">
        <h2>SehatCare ✚</h2>
        <p class="muted">Klinik Sehat untuk Keluarga<br>Jl. Kesehatan No. 10 · Tel. 0812-0000-0000</p>
    </div>
    <table>
        <tr>
            <td>Invoice</td>
            <td>: <?= e($b['no_invoice']) ?></td>
        </tr>
        <tr>
            <td>Pasien</td>
            <td>: <?= e($b['pasien'] . ' (' . $b['no_rm'] . ')') ?></td>
        </tr>
        <tr>
            <td>Dokter</td>
            <td>: <?= e($b['dokter']) ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: <?= date('d/m/Y H:i', strtotime($b['created_at'])) ?></td>
        </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td>Konsultasi & layanan medis</td>
            <td><?= rupiah($b['total'] - array_sum(array_map(fn($x) => $x['jumlah'] * $x['harga'], $items))) ?></td>
        </tr><?php foreach ($items as $x): ?>
            <tr>
                <td><?= e($x['nama']) ?> × <?= $x['jumlah'] ?></td>
                <td><?= rupiah($x['jumlah'] * $x['harga']) ?></td>
            </tr><?php endforeach; ?>
        <tr class="total">
            <td>TOTAL</td>
            <td><?= rupiah($b['total']) ?></td>
        </tr>
        <tr>
            <td>Bayar (<?= e($b['metode']) ?>)</td>
            <td><?= rupiah($b['bayar']) ?></td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td><?= rupiah($b['kembalian']) ?></td>
        </tr>
    </table>
    <p class="muted" style="text-align:center">Terima kasih. Semoga lekas sehat.</p>
    <div class="actions" style="justify-content:center"><button class="btn" onclick="window.print()">Cetak
            Struk</button><a class="btn secondary" href="index.php">Kembali</a></div>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>