<?php
$title = 'Pendaftaran & Pemeriksaan';
require_once __DIR__ . '/../includes/header.php';
$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
$pasien = $pdo->query('SELECT id,no_rm,nama FROM pasien ORDER BY nama')->fetchAll();
$dokter = $pdo->query('SELECT * FROM dokter ORDER BY nama')->fetchAll();
$obat = $pdo->query('SELECT * FROM obat WHERE stok>0 ORDER BY nama')->fetchAll();
$row = ['pasien_id' => '', 'dokter_id' => '', 'keluhan' => '', 'diagnosa' => '', 'tindakan' => '', 'biaya_tindakan' => 0, 'status' => 'menunggu'];
$used = [];
if ($id) {
  $s = $pdo->prepare('SELECT * FROM pemeriksaan WHERE id=?');
  $s->execute([$id]);
  $row = $s->fetch() ?: redirect('index.php');
  $s = $pdo->prepare('SELECT po.*,o.nama FROM pemeriksaan_obat po JOIN obat o ON o.id=po.obat_id WHERE pemeriksaan_id=?');
  $s->execute([$id]);
  $used = $s->fetchAll();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $medicineIds = $_POST['obat_id'] ?? [];
  $qtys = $_POST['jumlah'] ?? [];
  try {
    $pdo->beginTransaction();
    if ($id) {
      $pdo->prepare('UPDATE pemeriksaan SET dokter_id=?,keluhan=?,diagnosa=?,tindakan=?,biaya_tindakan=?,status=? WHERE id=?')->execute([(int) $_POST['dokter_id'], trim($_POST['keluhan']), trim($_POST['diagnosa']), trim($_POST['tindakan']), (float) $_POST['biaya_tindakan'], $_POST['status'], $id]);
    } else {
      $no = 'KJ-' . date('Ymd') . '-' . str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT);
      $pdo->prepare('INSERT INTO pemeriksaan(no_kunjungan,pasien_id,dokter_id,petugas_id,tanggal,keluhan,diagnosa,tindakan,biaya_tindakan,status) VALUES(?,?,?,?,NOW(),?,?,?,?,?)')->execute([$no, (int) $_POST['pasien_id'], (int) $_POST['dokter_id'], $_SESSION['user']['id'], trim($_POST['keluhan']), trim($_POST['diagnosa']), trim($_POST['tindakan']), (float) $_POST['biaya_tindakan'], $_POST['status']]);
      $id = (int) $pdo->lastInsertId();
      foreach ($medicineIds as $i => $obatId) {
        $q = (int) ($qtys[$i] ?? 0);
        if (!$obatId || $q < 1)
          continue;
        $s = $pdo->prepare('SELECT stok,harga FROM obat WHERE id=? FOR UPDATE');
        $s->execute([(int) $obatId]);
        $o = $s->fetch();
        if (!$o || $o['stok'] < $q)
          throw new Exception('Stok obat tidak mencukupi.');
        $pdo->prepare('INSERT INTO pemeriksaan_obat(pemeriksaan_id,obat_id,jumlah,harga) VALUES(?,?,?,?)')->execute([$id, $obatId, $q, $o['harga']]);
        $pdo->prepare('UPDATE obat SET stok=stok-? WHERE id=?')->execute([$q, $obatId]);
      }
    }
    $pdo->commit();
    flash('success', 'Data pemeriksaan tersimpan. Stok obat diperbarui otomatis.');
    redirect('index.php');
  } catch (Throwable $e) {
    if ($pdo->inTransaction())
      $pdo->rollBack();
    $error = $e->getMessage();
  }
}
?>
<section class="panel">
  <h2><?= $id ? 'Detail & Penyelesaian' : 'Pendaftaran Baru' ?></h2><?php if (isset($error)): ?>
    <div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <form class="grid" method="post"><label>Pasien<select name="pasien_id" required <?= $id ? 'disabled' : '' ?>>
        <option value="">Pilih pasien</option><?php foreach ($pasien as $p): ?>
          <option value="<?= $p['id'] ?>" <?= $row['pasien_id'] == $p['id'] ? 'selected' : '' ?>>
            <?= e($p['no_rm'] . ' — ' . $p['nama']) ?></option><?php endforeach; ?>
      </select></label><label>Dokter<select name="dokter_id" required><?php foreach ($dokter as $d): ?>
          <option value="<?= $d['id'] ?>" <?= $row['dokter_id'] == $d['id'] ? 'selected' : '' ?>>
            <?= e($d['nama'] . ' · ' . $d['spesialis']) ?></option><?php endforeach; ?>
      </select></label><label class="full">Keluhan<textarea name="keluhan"
        required><?= e($row['keluhan']) ?></textarea></label><label>Diagnosa<textarea
        name="diagnosa"><?= e($row['diagnosa']) ?></textarea></label><label>Tindakan / Layanan<textarea
        name="tindakan"><?= e($row['tindakan']) ?></textarea></label><label>Biaya Tindakan<input type="number" min="0"
        name="biaya_tindakan" value="<?= e($row['biaya_tindakan']) ?>"></label><label>Status<select name="status">
        <option value="menunggu" <?= $row['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
        <option value="diperiksa" <?= $row['status'] === 'diperiksa' ? 'selected' : '' ?>>Diperiksa</option>
        <option value="selesai" <?= $row['status'] === 'selesai' ? 'selected' : '' ?>>Selesai / Siap Bayar</option>
      </select></label><?php if (!$id): ?>
      <div class="full">
        <h2 style="margin-top:10px">Resep Obat <small class="muted">(opsional, stok berkurang otomatis)</small></h2>
        <div id="medicine">
          <div class="row med-row"><select name="obat_id[]">
              <option value="">Pilih obat</option><?php foreach ($obat as $o): ?>
                <option value="<?= $o['id'] ?>"><?= e($o['nama'] . ' (stok: ' . $o['stok'] . ') — ' . rupiah($o['harga'])) ?></option>
              <?php endforeach; ?>
            </select><input type="number" name="jumlah[]" min="1" value="1" style="width:100px"></div>
        </div><button type="button" class="btn secondary" style="margin-top:10px" onclick="addMed()">+ Tambah
          Obat</button>
      </div><?php else: ?>
      <div class="full">
        <h2>Obat Diresepkan</h2><?php if ($used): ?>
          <table><?php foreach ($used as $u): ?>
              <tr>
                <td><?= e($u['nama']) ?></td>
                <td><?= $u['jumlah'] ?> x <?= rupiah($u['harga']) ?></td>
                <td><?= rupiah($u['jumlah'] * $u['harga']) ?></td>
              </tr><?php endforeach; ?>
          </table><?php else: ?>
          <p class="muted">Tidak ada obat pada kunjungan ini.</p><?php endif; ?>
      </div><?php endif; ?>
    <div class="full actions"><button class="btn">Simpan Pemeriksaan</button><a class="btn secondary"
        href="index.php">Kembali</a></div>
  </form>
</section><?php if (!$id): ?>
  <script>function addMed() { let c = document.querySelector('.med-row').cloneNode(true); c.querySelector('select').value = ''; c.querySelector('input').value = 1; document.querySelector('#medicine').append(c) }</script>
<?php endif; ?><?php require __DIR__ . '/../includes/footer.php'; ?>