# PasarKita Security Lab — Non-patched vs Patched

Marketplace lokal bergaya toko online untuk membandingkan dua implementasi keamanan web. Aplikasi PHP ini memakai MySQL/MariaDB lokal dan tidak memerlukan Docker.

## Isi aplikasi

- **Beranda marketplace**: katalog produk demo, kategori dan kolom pencarian produk.
- **Pusat Seller → Data pelanggan**: pencarian pelanggan dengan parameter `q`. Pada mode Non-patched, input digabungkan ke query SQL; pada mode Patched, pencarian menggunakan prepared statement.
- **Pusat Seller → Verifikasi dokumen**: pada Non-patched, file diterima tanpa pemeriksaan tipe dan disimpan di direktori publik, sehingga file PHP dapat dieksekusi oleh server PHP lokal. Pada Patched, hanya JPG/PNG/PDF dengan pemeriksaan ekstensi + MIME yang diterima, nama file diacak, dan file disimpan di luar webroot.
- Menu **Mode aplikasi** berpindah antara Non-patched dan Patched pada semua halaman.
- `payloads/command-demo.php` adalah payload demonstrasi tetap untuk lab lokal; hanya menjalankan perintah identitas (`whoami`/`id`) dan menampilkan CSV fiktif.

## Menjalankan di Windows dengan XAMPP (tanpa Docker)

1. Pasang XAMPP dari https://www.apachefriends.org/download.html.
2. Buka XAMPP Control Panel dan klik **Start** pada MySQL. Pastikan statusnya Running. Jika MySQL berjalan pada port lain, ubah `DB_PORT` di `src/config.php`.
3. Ekstrak folder `pasarkita-security-lab` dan buka folder itu di Antigravity.
4. Pastikan `src/config.php` cocok dengan setelan database lokal. Default XAMPP: host `127.0.0.1`, port `3306`, user `root`, password kosong.
5. Di terminal PowerShell Antigravity, jalankan:

   ```powershell
   .\run-lab.ps1
   ```

   Atau jalankan PHP langsung:

   ```powershell
   & 'C:\xampp\php\php.exe' -S 127.0.0.1:8080 -t public
   ```

6. Buka http://127.0.0.1:8080.

Database `pasarkita_lab`, tabel `pelanggan`, dan data contoh biasanya dibuat saat halaman **Data pelanggan** pertama kali dibuka. Jika akun MySQL kamu tidak punya hak `CREATE DATABASE`, buka phpMyAdmin dan import `database/setup.sql`, lalu muat ulang halaman.

## Latihan

1. Di header, pastikan **Non-patched** aktif.
2. Buka **Pusat Seller → Data pelanggan**. Cari `Andi`, lalu amati hasilnya.
3. Untuk dataset fiktif ini, input `' OR 1=1 -- ` pada kolom cari pelanggan mengubah kondisi query di mode Non-patched.
4. Pindah ke **Patched** dan coba input yang sama. Prepared statement memperlakukannya sebagai teks pencarian biasa.
5. Buka **Pusat Seller → Verifikasi dokumen**. Pada mode Non-patched, `payloads/command-demo.php` dapat diunggah untuk mengamati eksekusi PHP pada lab lokal. Pindah ke Patched dan file PHP ditolak; hanya JPG/PNG/PDF dengan MIME yang sesuai diterima.

## Troubleshooting koneksi database

- `No connection could be made because the target machine actively refused it`: Start MySQL di XAMPP dan pastikan port `DB_PORT` benar.
- `could not find driver`: aktifkan `extension=pdo_mysql` di `php.ini` yang dipakai PHP, lalu restart server PHP.
- `CREATE DATABASE command denied`: import `database/setup.sql` melalui phpMyAdmin menggunakan akun yang berhak membuat database, lalu coba ulang.
- Jika MySQL di XAMPP menggunakan port `3307`, atur `DB_PORT = 3307` pada `src/config.php` dan gunakan port itu secara konsisten.

## Catatan keamanan

- Ini sengaja berisi mode rentan untuk pendidikan. **Jangan deploy ke hosting, jangan buka port ke jaringan/internet, dan jangan gunakan data pelanggan nyata.**
- Server PHP dijalankan pada `127.0.0.1:8080` agar hanya bisa diakses dari komputer lokal.
- Dataset pelanggan sepenuhnya fiktif. CSV ada di `data/data_pelanggan.csv`; pada Linux, file `/root/data_pelanggan.csv` dipakai hanya jika dapat dibaca. Jangan jalankan server PHP sebagai root untuk mengakses file tersebut.
- Mode Patched memperbaiki dua modul contoh ini, tetapi bukan sertifikasi bahwa seluruh aplikasi production-ready.


## Catatan UI untuk latihan SQL Injection

Pada versi ini, halaman Data Pelanggan tidak menampilkan seluruh pelanggan otomatis. Masukkan kata kunci terlebih dahulu. Ini membuat perbedaan perilaku lebih jelas: di mode Non-patched, input yang mengubah kondisi SQL dapat memperluas hasil; di mode Patched, input diperlakukan sebagai nilai parameter pencarian. Semua pengujian hanya untuk localhost dan data fiktif.
