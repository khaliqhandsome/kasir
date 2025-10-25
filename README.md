# Kasir UMKM

Aplikasi kasir web berbasis PHP untuk usaha kecil dengan tampilan responsif ala mobile menggunakan template [Start Bootstrap SB Admin 2](https://github.com/StartBootstrap/startbootstrap-sb-admin-2).

## Fitur

- Dashboard ringkas untuk memantau penjualan harian.
- Pencatatan transaksi dengan kalkulasi pajak otomatis dan hitung kembalian.
- Cetak struk siap print dengan tampilan bersih.
- Kelola katalog produk sederhana.
- Laporan penjualan dasar dengan rentang tanggal.

## Teknologi

- PHP 8 dengan PDO MySQL.
- Template Start Bootstrap SB Admin 2 (melalui CDN).
- MySQL untuk menyimpan produk, transaksi, dan item penjualan.

## Menjalankan aplikasi

1. Pastikan PHP dan MySQL aktif (contoh: paket XAMPP dengan Apache + MySQL).
2. Salin proyek ini ke folder web server Anda, misalnya `htdocs/kasir` pada instalasi XAMPP Windows.
3. (Opsional) Salin `config/database.example.php` menjadi `config/database.php` lalu sesuaikan kredensial MySQL jika berbeda dari bawaan XAMPP (`user: root`, `password: kosong`). Anda juga dapat menggunakan variabel lingkungan `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, dan `DB_PORT`.
4. Nyalakan Apache dan MySQL dari XAMPP Control Panel, kemudian akses `http://localhost/kasir/public`.

Untuk menjalankan lewat server bawaan PHP tanpa Apache:

```bash
php -S localhost:8000 -t public
```

Lalu buka `http://localhost:8000` di browser. Selama MySQL berjalan dan kredensial benar, aplikasi otomatis membuat database `kasir` (jika belum ada), membuat tabel, dan mengisi beberapa data produk contoh saat pertama kali diakses.

## Struktur folder

- `app/` – bootstrap aplikasi, koneksi database, serta helper.
- `public/` – file PHP yang dapat diakses publik dan aset tampilan.
- `config/` – contoh konfigurasi database yang bisa disalin untuk menyesuaikan kredensial.

Selamat menggunakan!
