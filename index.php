<?php
require_once __DIR__ . '/config/app.php';
require_login();
if ($_SESSION['user']['role'] === 'user')
    redirect('/CRUDP/user/index.php');
$title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
$pdo = db();
$patients = $pdo->query('SELECT COUNT(*) FROM pasien')->fetchColumn();
$medicine = $pdo->query('SELECT COUNT(*) FROM obat')->fetchColumn();
$waiting = $pdo->query("SELECT COUNT(*) FROM pemeriksaan WHERE status IN ('menunggu','diperiksa','selesai')")->fetchColumn();
$income = $pdo->query('SELECT COALESCE(SUM(total),0) FROM pembayaran WHERE DATE(created_at)=CURDATE()')->fetchColumn();
$visits = $pdo->query("SELECT p.no_kunjungan,p.tanggal,p.status,ps.nama pasien,d.nama dokter FROM pemeriksaan p JOIN pasien ps ON ps.id=p.pasien_id JOIN dokter d ON d.id=p.dokter_id ORDER BY p.id DESC LIMIT 7")->fetchAll();
?>
<section class="grid stats">
    <div class="card"><span class="stat-icon">♙</span><span class="stat-label">Total Pasien</span>
        <div class="stat-value"><?= $patients ?></div>
    </div>
    <div class="card"><span class="stat-icon">✚</span><span class="stat-label">Kunjungan Aktif</span>
        <div class="stat-value"><?= $waiting ?></div>
    </div>
    <div class="card"><span class="stat-icon">▣</span><span class="stat-label">Jenis Obat</span>
        <div class="stat-value"><?= $medicine ?></div>
    </div>
    <div class="card"><span class="stat-icon">₿</span><span class="stat-label">Pendapatan Hari Ini</span>
        <div class="stat-value" style="font-size:19px"><?= rupiah($income) ?></div>
    </div>
</section>
<section class="panel" style="margin-top:22px">
    <div class="row">
        <h2>Kunjungan Terbaru</h2><a class="btn" href="/CRUDP/pemeriksaan/form.php">+ Daftarkan Pasien</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>NO. KUNJUNGAN</th>
                <th>PASIEN</th>
                <th>DOKTER</th>
                <th>TANGGAL</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody><?php foreach ($visits as $v): ?>
                <tr>
                    <td><?= e($v['no_kunjungan']) ?></td>
                    <td><?= e($v['pasien']) ?></td>
                    <td><?= e($v['dokter']) ?></td>
                    <td><?= date('d M Y H:i', strtotime($v['tanggal'])) ?></td>
                    <td><span
                            class="badge <?= in_array($v['status'], ['dibayar', 'selesai']) ? 'done' : '' ?>"><?= e(ucfirst($v['status'])) ?></span>
                    </td>
                </tr><?php endforeach; ?><?php if (!$visits): ?>
                <tr>
                    <td colspan="5" class="muted">Belum ada kunjungan. Mulai dengan mendaftarkan pasien.</td>
                </tr><?php endif; ?>
        </tbody>
    </table>
</section><?php require __DIR__ . '/includes/footer.php'; ?>