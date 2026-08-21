# SehatCare — Sistem Informasi Klinik

SehatCare adalah aplikasi manajemen klinik berbasis PHP native dan MySQL. Aplikasi ini membantu staf mencatat pasien, kunjungan dan pemeriksaan, resep serta stok obat, pembayaran, dan struk. Pelanggan juga dapat membuat akun, mengajukan janji pemeriksaan, dan melihat riwayat kunjungannya.

## Fitur

- Dashboard ringkasan pasien, kunjungan aktif, stok obat, dan pendapatan hari ini.
- Pengelolaan data pasien dan pencarian berdasarkan nama atau nomor rekam medis.
- Pengelolaan obat dan stok; hanya administrator yang dapat menambah, mengubah, atau menghapus obat.
- Pendaftaran kunjungan, pencatatan keluhan, diagnosis, tindakan, dan resep obat.
- Pengurangan stok obat otomatis saat resep disimpan, dalam transaksi database.
- Perhitungan tagihan konsultasi, tindakan, dan obat; pembayaran tunai, transfer, atau QRIS.
- Struk pembayaran yang dapat dicetak.
- Portal pelanggan untuk membuat janji dan memantau riwayat pemeriksaan.
- Manajemen daftar pengguna untuk administrator.

## Peran pengguna

| Peran | Hak akses utama |
| --- | --- |
| `admin` | Seluruh fitur staf, termasuk manajemen obat dan daftar pengguna. |
| `petugas` | Mengelola pasien, pemeriksaan, pembayaran, dan melihat persediaan obat. |
| `user` | Portal pelanggan: membuat janji, melihat kunjungan, dan melakukan pembayaran kunjungan sendiri. |

## Kebutuhan

- PHP 7.4 atau lebih baru dengan ekstensi `pdo_mysql`.
- MySQL atau MariaDB.
- Web server lokal, misalnya Apache dari XAMPP.

## Instalasi lokal

1. Salin atau letakkan proyek di folder web server, misalnya `htdocs/CRUDP` pada XAMPP.
2. Buat database MySQL bernama `klinik_sehatcare`.
3. Impor skema dan data awal aplikasi ke database tersebut.
4. Atur kredensial database pada [config/database.php](config/database.php).
5. Jalankan Apache dan MySQL, kemudian buka `http://localhost/CRUDP/auth/login.php`.

> Catatan: repositori ini saat ini tidak menyertakan berkas dump/skema SQL. Sebelum aplikasi dapat dijalankan, database harus menyediakan tabel yang digunakan aplikasi: `users`, `pasien`, `dokter`, `obat`, `pemeriksaan`, `pemeriksaan_obat`, dan `pembayaran`.

## Konfigurasi database

Ubah konstanta berikut pada `config/database.php` sesuai lingkungan Anda:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'klinik_sehatcare');
define('DB_USER', 'root');
define('DB_PASS', '');
```

URL dan pengalihan aplikasi saat ini menggunakan prefix `/CRUDP`. Jika folder proyek menggunakan nama lain, sesuaikan semua referensi `/CRUDP/...` pada berkas PHP terkait.

## Akun demo

Apabila data awal database telah dibuat, akun berikut dapat digunakan:

| Peran | Username | Password |
| --- | --- | --- |
| Administrator | `admin` | `password` |
| Petugas | `petugas` | `password` |

Pelanggan dapat membuat akun sendiri melalui tautan **Buat akun** pada halaman masuk. Proses ini membuat akun berperan `user` sekaligus data pasien yang terhubung.

## Alur penggunaan

1. Administrator menyiapkan data obat dan stok. Petugas dapat menambahkan data pasien bila diperlukan.
2. Petugas membuat pendaftaran baru, atau pelanggan mengirim permintaan janji dari portalnya.
3. Petugas melengkapi pemeriksaan: keluhan, diagnosis, tindakan, status, dan resep obat.
4. Saat resep pada kunjungan baru disimpan, stok obat dikurangi otomatis.
5. Petugas atau pelanggan melanjutkan ke pembayaran. Total dihitung dari tarif dokter, biaya tindakan, dan obat.
6. Setelah pembayaran tersimpan, status kunjungan menjadi `dibayar` dan struk dapat dicetak.

## Catatan pengembangan dan keamanan

- Aplikasi menggunakan PDO prepared statement untuk query yang menerima masukan pengguna.
- Pembaruan resep/stok dan penyimpanan pembayaran menggunakan transaksi database.
- `config/app.php` mengaktifkan tampilan error PHP untuk pengembangan lokal. Nonaktifkan `display_errors` pada lingkungan produksi.
- Jangan memakai kredensial database atau akun demo bawaan pada lingkungan produksi; ganti dengan nilai yang aman.

# tugas_21_08_2026
# tugas_21_08_2026
