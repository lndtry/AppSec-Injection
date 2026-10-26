# SEC//LAB — Local Vulnerability Lab

Lab keamanan lokal (XAMPP) berisi dua kerentanan yang **sengaja** dibuat untuk pembelajaran:
**SQL Injection** dan **Command Injection**. Tiap modul punya versi `VULNERABLE` dan `PATCHED`.

> ## ⚠️ PERINGATAN
> Aplikasi ini **sengaja dibuat rentan** (SQL Injection & Command Injection) untuk tujuan
> edukasi / tugas mata kuliah *Secure App Development*.
> **JANGAN** di-deploy ke server publik atau dipakai di lingkungan produksi. Jalankan hanya di
> `localhost` (XAMPP) dengan data dummy. Penulis tidak bertanggung jawab atas penyalahgunaan.

> Hanya untuk `localhost`. Jangan di-deploy ke server publik. Pakai data dummy.

## Setup

1. Copy folder `lab-app` ke `C:\xampp\htdocs\` (hasil: `C:\xampp\htdocs\lab-app`).
2. Jalankan XAMPP → start **Apache** dan **MySQL**.
3. Import database: buka `http://localhost/phpmyadmin` → tab **Import** → pilih `db.sql` → **Go**.
   (Atau lewat CLI: `C:\xampp\mysql\bin\mysql -u root < db.sql`)
4. Buka `http://localhost/lab-app/`.

Kalau nama folder beda dari `lab-app`, tidak perlu diubah — base URL terdeteksi otomatis dari `DOCUMENT_ROOT`.
Kredensial DB ada di `config.php` (default XAMPP: user `root`, password kosong).

## Modul 1 — SQL Injection

Kode rentan (`vulnerable/login.php`):

```php
$query = "SELECT id, username, role, email FROM users WHERE username = '$u' AND password = '$p'";
```

Payload uji (query memilih 4 kolom, jadi UNION juga harus 4 kolom):

| Input | Efek |
|-------|------|
| username `admin' -- ` | login sebagai admin tanpa password |
| username `' OR '1'='1' -- ` | dump seluruh tabel `users` (8 row) |
| username `' UNION SELECT 1,username,password,'-' FROM users -- ` | dump kredensial semua user |
| username `' UNION SELECT 1,label,value,'-' FROM secrets -- ` | dump tabel `secrets` (flag/API key) |

Perbaikan (`secure/login.php`): **prepared statement** + `bind_param()`, sehingga input tidak
pernah menyentuh string query:

```php
$stmt = $conn->prepare('SELECT id, username, role, email FROM users WHERE username = ? AND password = ?');
$stmt->bind_param('ss', $u, $p);
$stmt->execute();
```

> Catatan implementasi: sejak PHP 8.1, `mysqli->query()` **melempar `mysqli_sql_exception`**
> saat query error (bukan mengembalikan `false`). Versi rentan menangkap exception ini agar
> payload yang syntax-error tetap menampilkan pesan `QUERY ERROR` yang rapi, bukan fatal error.

## Modul 2 — Command Injection

Kode rentan (`vulnerable/ping.php`):

```php
$cmd = 'ping -n 1 ' . $_GET['host'];
$out = shell_exec($cmd);
```

Payload uji (Windows, separator `&`):

| Input | Efek |
|-------|------|
| `127.0.0.1 & whoami` | jalankan `whoami` |
| `127.0.0.1 & dir` | list direktori |
| `127.0.0.1 & ipconfig` | lihat konfigurasi jaringan |

Perbaikan (`secure/ping.php`): **whitelist** regex + `escapeshellarg()`:

```php
if (!preg_match('/^[a-zA-Z0-9.\-]{1,255}$/', $host)) { /* tolak */ }
$cmd = 'ping -n 1 ' . escapeshellarg($host);
```

## Catatan

- Di Linux, separator command injection adalah `;` dan `ping` tanpa flag `-n`.
- `shell_exec` berjalan sebagai user Apache — batasi lab ke mesin lokal.
- Kalau `shell_exec` dimatikan di `php.ini` (`disable_functions`), modul 2 tidak akan mengeksekusi apa pun.
- Judul/heading memakai gaya dark monotone Japan-cyberpunk; bagian visual murni di `assets/style.css`.
- Data dummy: tabel `users` (8 baris, kolom id/username/password/role/email/created_at) dan tabel `secrets` (flag, api_key, db_backup).
