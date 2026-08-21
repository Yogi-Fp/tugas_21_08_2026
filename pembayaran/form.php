<?php $title = 'Penyelesaian Pembayaran';
require_once __DIR__ . '/../includes/header.php';
$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
$s = $pdo->prepare('SELECT k.*,p.nama pasien,p.no_rm,d.nama dokter,d.tarif FROM pemeriksaan k JOIN pasien p ON p.id=k.pasien_id JOIN dokter d ON d.id=k.dokter_id WHERE k.id=?');
$s->execute([$id]);
$k = $s->fetch() ?: redirect('/CRUDP/pemeriksaan/index.php');
if ($k['status'] === 'dibayar') {
    flash('error', 'Kunjungan ini sudah dibayar.');
    redirect('index.php');
}
$s = $pdo->prepare('SELECT po.*,o.nama FROM pemeriksaan_obat po JOIN obat o ON o.id=po.obat_id WHERE pemeriksaan_id=?');
$s->execute([$id]);
$items = $s->fetchAll();
$obatTotal = array_sum(array_map(fn($x) => $x['jumlah'] * $x['harga'], $items));
$total = $k['tarif'] + $k['biaya_tindakan'] + $obatTotal;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bayar = (float) $_POST['bayar'];
    if ($bayar < $total)
        $error = 'Nominal pembayaran kurang dari total tagihan.';
    else {
        try {
            $pdo->beginTransaction();
            $invoice = 'INV-' . date('Ymd') . '-' . str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT);
            $pdo->prepare('INSERT INTO pembayaran(no_invoice,pemeriksaan_id,total,bayar,kembalian,metode) VALUES(?,?,?,?,?,?)')->execute([$invoice, $id, $total, $bayar, $bayar - $total, $_POST['metode']]);
            $pdo->prepare("UPDATE pemeriksaan SET status='dibayar' WHERE id=?")->execute([$id]);
            $pid = $pdo->lastInsertId();
            $pdo->commit();
            redirect('struk.php?id=' . $pid);
        } catch (Throwable $e) {
            if ($pdo->inTransaction())
                $pdo->rollBack();
            $error = 'Pembayaran gagal diproses.';
        }
    }
} ?>
<section class="panel">
    <h2>Tagihan <?= e($k['no_kunjungan']) ?></h2><?php if (isset($error)): ?>
        <div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <div class="grid">
        <div>
            <p class="muted">PASIEN</p><b><?= e($k['pasien']) ?></b><br><span class="muted"><?= e($k['no_rm']) ?></span>
        </div>
        <div>
            <p class="muted">DOKTER</p><b><?= e($k['dokter']) ?></b>
        </div>
    </div>
    <table style="margin:24px 0">
        <tr>
            <td>Konsultasi Dokter</td>
            <td><?= rupiah($k['tarif']) ?></td>
        </tr>
        <tr>
            <td>Tindakan / Layanan</td>
            <td><?= rupiah($k['biaya_tindakan']) ?></td>
        </tr><?php foreach ($items as $x): ?>
            <tr>
                <td><?= e($x['nama']) ?> × <?= $x['jumlah'] ?></td>
                <td><?= rupiah($x['harga'] * $x['jumlah']) ?></td>
            </tr><?php endforeach; ?>
        <tr class="total">
            <td>Total Tagihan</td>
            <td><?= rupiah($total) ?></td>
        </tr>
    </table>
    <form method="post" class="grid"><label>Metode Pembayaran<select name="metode">
                <option>Tunai</option>
                <option>Transfer</option>
                <option>QRIS</option>
            </select></label><label>Nominal Dibayar<input type="number" min="<?= ceil($total) ?>" name="bayar"
                value="<?= ceil($total) ?>" required></label>
        <div class="full actions"><button class="btn">Simpan Pembayaran & Cetak Struk</button><a class="btn secondary"
                href="/CRUDP/pemeriksaan/index.php">Batal</a></div>
    </form>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>