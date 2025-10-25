# Kasir UMKM

Aplikasi kasir web berbasis PHP untuk usaha kecil dengan tampilan responsif ala mobile menggunakan template [Start Bootstrap SB Admin 2](https://github.com/StartBootstrap/startbootstrap-sb-admin-2).

## Fitur

- Dashboard ringkas untuk memantau penjualan harian.
- Pencatatan transaksi dengan kalkulasi pajak otomatis dan hitung kembalian.
- Cetak struk siap print dengan tampilan bersih.
- Kelola katalog produk sederhana.
- Laporan penjualan dasar dengan rentang tanggal.

## Teknologi

- PHP 8 dengan PDO SQLite (database file lokal).
- Template Start Bootstrap SB Admin 2 (melalui CDN).
- SQLite digunakan untuk menyimpan produk, transaksi, dan item penjualan.

## Menjalankan aplikasi

1. Pastikan PHP terpasang.
2. Jalankan server pengembangan PHP dari direktori proyek:

   ```bash
   php -S localhost:8000 -t public
   ```

3. Buka `http://localhost:8000` di browser (dapat diakses melalui perangkat mobile yang terhubung ke jaringan yang sama).

Database SQLite akan dibuat otomatis pada folder `data/` saat aplikasi pertama kali dijalankan.

## Struktur folder

- `app/` – bootstrap aplikasi, koneksi database, serta helper.
- `public/` – file PHP yang dapat diakses publik dan aset tampilan.
- `data/` – file database SQLite.

Selamat menggunakan!
