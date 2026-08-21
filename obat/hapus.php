<?php require_once __DIR__ . '/../config/app.php';
require_admin();
try {
    db()->prepare('DELETE FROM obat WHERE id=?')->execute([(int) $_GET['id']]);
    flash('success', 'Obat dihapus.');
} catch (PDOException $e) {
    flash('error', 'Obat tidak dapat dihapus karena dipakai pada pemeriksaan.');
}
redirect('/CRUDP/obat/index.php');
