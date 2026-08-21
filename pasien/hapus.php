<?php require_once __DIR__ . '/../config/app.php';
require_admin();
try {
    db()->prepare('DELETE FROM pasien WHERE id=?')->execute([(int) $_GET['id']]);
    flash('success', 'Data pasien dihapus.');
} catch (PDOException $e) {
    flash('error', 'Pasien tidak dapat dihapus karena sudah memiliki riwayat kunjungan.');
}
redirect('/CRUDP/pasien/index.php');
