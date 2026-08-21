<?php $title = 'Buat Janji Pemeriksaan';
require_once __DIR__ . '/../includes/header.php';
$pdo = db();
$s = $pdo->prepare('SELECT id FROM pasien WHERE user_id=?');
$s->execute([$_SESSION['user']['id']]);
$pasienId = $s->fetchColumn();
$dokter = $pdo->query('SELECT * FROM dokter ORDER BY nama')->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $no = 'KJ-' . date('Ymd') . '-' . str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT);
        $pdo->prepare("INSERT INTO pemeriksaan(no_kunjungan,pasien_id,dokter_id,petugas_id,tanggal,keluhan,status) VALUES(?,?,?,(SELECT id FROM users WHERE role='petugas' LIMIT 1),?,?,?)")->execute([$no, $pasienId, (int) $_POST['dokter_id'], $_POST['tanggal'], trim($_POST['keluhan']), 'menunggu']);
        flash('success', 'Janji pemeriksaan berhasil dikirim.');
        redirect('index.php');
    } catch (Throwable $e) {
        $error = 'Gagal membuat janji. Pastikan data sudah lengkap.';
    }
} ?><a
    class="back-link" href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke portal</a>
<section class="panel">
    <h2>Ajukan Janji Pemeriksaan</h2>
    <p class="muted">Petugas klinik akan memproses pengajuan Anda.</p><?php if (isset($error)): ?>
        <div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="grid"><label>Dokter<select name="dokter_id" required><?php foreach ($dokter as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= e($d['nama'] . ' · ' . $d['spesialis'] . ' · ' . rupiah($d['tarif'])) ?>
                    </option><?php endforeach; ?>
            </select></label><label>Waktu yang Diinginkan<input type="datetime-local" name="tanggal" required
                min="<?= date('Y-m-d\TH:i') ?>"></label><label class="full">Keluhan<textarea name="keluhan" required
                placeholder="Jelaskan keluhan utama Anda..."></textarea></label>
        <div class="full actions"><button class="btn"><i class="fa-solid fa-paper-plane"></i> Kirim
                Permintaan</button><a class="btn secondary" href="index.php">Batal</a></div>
    </form>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>