<?php require_once __DIR__ . '/../config/app.php';
require_admin();
$title = 'Form Obat';
require_once __DIR__ . '/../includes/header.php';
$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
$row = ['kode' => 'OBT-' . str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT), 'nama' => '', 'satuan' => 'Tablet', 'stok' => 0, 'harga' => 0];
if ($id) {
    $s = $pdo->prepare('SELECT * FROM obat WHERE id=?');
    $s->execute([$id]);
    $row = $s->fetch() ?: redirect('index.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [trim($_POST['kode']), trim($_POST['nama']), trim($_POST['satuan']), (int) $_POST['stok'], (float) $_POST['harga']];
        if ($id) {
            $data[] = $id;
            $pdo->prepare('UPDATE obat SET kode=?,nama=?,satuan=?,stok=?,harga=? WHERE id=?')->execute($data);
        } else
            $pdo->prepare('INSERT INTO obat(kode,nama,satuan,stok,harga) VALUES(?,?,?,?,?)')->execute($data);
        flash('success', 'Data obat berhasil disimpan.');
        redirect('index.php');
    } catch (PDOException $e) {
        $error = 'Kode obat sudah digunakan.';
    }
} ?>
<section class="panel">
    <h2><?= $id ? 'Ubah' : 'Tambah' ?> Obat</h2><?php if (isset($error)): ?>
        <div class="alert error"><?= $error ?></div><?php endif; ?>
    <form class="grid" method="post"><label>Kode Obat<input name="kode" required
                value="<?= e($row['kode']) ?>"></label><label>Nama Obat<input name="nama" required
                value="<?= e($row['nama']) ?>"></label><label>Satuan<input name="satuan" required
                value="<?= e($row['satuan']) ?>"></label><label>Stok<input type="number" min="0" name="stok" required
                value="<?= e($row['stok']) ?>"></label><label>Harga Satuan<input type="number" min="0" name="harga"
                required value="<?= e($row['harga']) ?>"></label>
        <div class="full actions"><button class="btn">Simpan Data</button><a class="btn secondary"
                href="index.php">Batal</a></div>
    </form>
</section><?php require __DIR__ . '/../includes/footer.php'; ?>