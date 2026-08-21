<?php $title = 'Form Pasien';
require_once __DIR__ . '/../includes/header.php';
$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
$row = ['no_rm' => 'RM-' . date('ymd') . '-' . str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT), 'nama' => '', 'jenis_kelamin' => 'L', 'tanggal_lahir' => ''];
if ($id) {
    $s = $pdo->prepare('SELECT * FROM pasien WHERE id=?');
    $s->execute([$id]);
    $row = $s->fetch() ?: redirect('index.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [trim($_POST['no_rm']), trim($_POST['nama']), $_POST['jenis_kelamin'], $_POST['tanggal_lahir'] ?: null, $id];
        if ($id)
            $pdo->prepare('UPDATE pasien SET no_rm=?,nama=?,jenis_kelamin=?,tanggal_lahir=? WHERE id=?')->execute($data);
        else
            $pdo->prepare('INSERT INTO pasien(no_rm,nama,jenis_kelamin,tanggal_lahir) VALUES(?,?,?,?)')->execute(array_slice($data, 0, 4));
        flash('success', 'Data pasien berhasil disimpan.');
        redirect('index.php');
    } catch (PDOException $e) {
        $error = 'No. RM sudah digunakan.';
    }
} ?>
<section class="panel"><a class="back-link" href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke data
        pasien</a>
    <h2><?= $id ? 'Ubah' : 'Tambah' ?> Pasien</h2><?php if (isset($error)): ?>
        <div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="grid"><label>No. Rekam Medis<input name="no_rm" required
                value="<?= e($row['no_rm']) ?>"></label><label>Nama Lengkap<input name="nama" required
                value="<?= e($row['nama']) ?>"></label><label>Jenis Kelamin<select name="jenis_kelamin">
                <option value="L" <?= $row['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= $row['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select></label><label>Tanggal Lahir<input type="date" name="tanggal_lahir"
                value="<?= e($row['tanggal_lahir']) ?>"></label>
        <div class="full actions"><button class="btn">Simpan Data</button><a class="btn secondary"
                href="index.php">Batal</a></div>
    </form>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>