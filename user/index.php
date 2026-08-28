<?php $title = 'Portal Pelanggan';
require_once __DIR__ . '/../includes/header.php';
$pdo = db();
$s = $pdo->prepare('SELECT * FROM pasien WHERE user_id=?');
$s->execute([$_SESSION['user']['id']]);
$pasien = $s->fetch();
$visits = [];
if ($pasien) {
    $s = $pdo->prepare('SELECT k.*,d.nama dokter FROM pemeriksaan k JOIN dokter d ON d.id=k.dokter_id WHERE k.pasien_id=? ORDER BY k.id DESC');
    $s->execute([$pasien['id']]);
    $visits = $s->fetchAll();
}
$active = count(array_filter($visits, fn($v) => $v['status'] !== 'dibayar')); ?>
<section class="panel portal-hero">
    <h2>Halo, <?= e($_SESSION['user']['nama']) ?> 👋</h2>
    <p class="muted">Selamat datang di portal kesehatan pribadi Anda.</p><a class="btn" href="booking.php"><i
            class="fa-solid fa-calendar-plus"></i> Buat Janji Pemeriksaan</a>
</section>
<section class="grid stats">
    <div class="card"><span class="stat-label">No. Rekam Medis</span>
        <div class="stat-value" style="font-size:20px"><?= e($pasien['no_rm'] ?? '-') ?></div>
    </div>
    <div class="card"><span class="stat-label">Total Kunjungan</span>
        <div class="stat-value"><?= count($visits) ?></div>
    </div>
    <div class="card"><span class="stat-label">Kunjungan Berjalan</span>
        <div class="stat-value"><?= $active ?></div>
    </div>
</section>
<section class="panel" style="margin-top:22px">
    <h2>Riwayat Pemeriksaan Saya</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>DOKTER</th>
                    <th>KELUHAN</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody><?php foreach ($visits as $v): ?>
                    <tr>
                        <td><?= date('d M Y H:i', strtotime($v['tanggal'])) ?></td>
                        <td><?= e($v['dokter']) ?></td>
                        <td><?= e($v['keluhan']) ?></td>
                        <td><span class="badge <?= $v['status'] === 'dibayar' ? 'done' : '' ?>"><?= e(ucfirst($v['status'])) ?></span>
                        </td>
                        <td><?php if (in_array($v['status'], ['selesai', 'diperiksa'], true)): ?><a class="btn warning"
                                    href="/pembayaran/form.php?id=<?= $v['id'] ?>">Bayar
                                    Sekarang</a><?php elseif ($v['status'] === 'menunggu'): ?><span class="muted">Menunggu proses
                                    klinik</span><?php else: ?><span class="badge done">Lunas</span><?php endif; ?></td>
                    </tr><?php endforeach; ?><?php if (!$visits): ?>
                    <tr>
                        <td colspan="5" class="muted">Belum ada kunjungan. Silakan buat janji pemeriksaan.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>