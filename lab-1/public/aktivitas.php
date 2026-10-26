<?php
require_once __DIR__ . '/../src/view.php';

// Entry point NON-FORM untuk demonstrasi SQL injection:
//  1) Header HTTP  : X-Forwarded-For dan User-Agent -> query INSERT
//  2) Cookie       : nilai cookie "kota" -> query SELECT

$ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
$ip = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'));

// Set cookie "kota" lewat tombol, lalu query membacanya dari $_COOKIE pada load berikutnya.
if (isset($_GET['set_kota'])) {
    setcookie('kota', (string) $_GET['set_kota'], 0, '/');
    $_COOKIE['kota'] = (string) $_GET['set_kota'];
}
$kota = (string) ($_COOKIE['kota'] ?? '');

$logRows = [];
$insertSql = '';
$errorMessage = '';
$connectionHint = '';

$kotaRows = [];
$selectSql = '';
$kotaError = '';

try {
    $db = lab_db();
    $db->exec(
        'CREATE TABLE IF NOT EXISTS kunjungan (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            waktu DATETIME NOT NULL,
            ip VARCHAR(255) NOT NULL,
            perangkat VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    // ----- Entry point 1: HEADER HTTP -> INSERT -----
    if (is_patched()) {
        $insertSql = 'INSERT INTO kunjungan (waktu, ip, perangkat) VALUES (NOW(), :ip, :ua)';
        $stmt = $db->prepare($insertSql);
        $stmt->execute(['ip' => $ip, 'ua' => $ua]);
    } else {
        // LAB ONLY: nilai header dirangkai langsung ke SQL (header-based SQLi).
        $insertSql = "INSERT INTO kunjungan (waktu, ip, perangkat) VALUES (NOW(), '{$ip}', '{$ua}')";
        $db->exec($insertSql);
    }

    $logRows = $db->query('SELECT id, waktu, ip, perangkat FROM kunjungan ORDER BY id DESC LIMIT 15')->fetchAll();
} catch (Throwable $ex) {
    $errorMessage = db_error_hint($ex);
    $connectionHint = $ex->getMessage();
}

// ----- Entry point 2: COOKIE -> SELECT -----
if ($kota !== '') {
    try {
        $db = lab_db();
        if (is_patched()) {
            $selectSql = 'SELECT id, nama, email, alamat FROM pelanggan WHERE alamat = :kota';
            $stmt = $db->prepare($selectSql);
            $stmt->execute(['kota' => $kota]);
            $kotaRows = $stmt->fetchAll();
        } else {
            // LAB ONLY: nilai cookie dirangkai langsung ke SQL (cookie-based SQLi).
            $selectSql = "SELECT id, nama, email, alamat FROM pelanggan WHERE alamat = '{$kota}'";
            $kotaRows = $db->query($selectSql)->fetchAll();
        }
    } catch (Throwable $ex) {
        $kotaError = $ex->getMessage();
    }
}

render_header('Aktivitas', 'aktivitas');
?>
<div class="page-intro"><div class="breadcrumbs"><a href="<?= e(url_for('index.php')) ?>">Beranda</a><span>/</span><span>Pusat Seller</span><span>/</span><strong>Aktivitas</strong></div><div class="intro-row"><div><span class="eyebrow">PUSAT SELLER / MONITORING</span><h1>Aktivitas & log kunjungan</h1><p>Entry point SQL injection selain kolom form: lewat <b>header HTTP</b> dan <b>cookie</b>.</p></div><div class="intro-stat"><span class="stat-icon">◷</span><span><strong><?= $errorMessage === '' ? count($logRows) : '—' ?></strong><small>Log terbaru</small></span></div></div></div>
<div class="customer-layout"><aside class="seller-sidebar"><div class="sidebar-user"><div class="seller-avatar">PK</div><div><strong>PasarKita Seller</strong><small>Demo workspace</small></div><span class="online-dot"></span></div><a class="sidebar-link" href="<?= e(url_for('customers.php')) ?>"><span>▦</span>Data pelanggan</a><a class="sidebar-link" href="<?= e(url_for('upload.php')) ?>"><span>⇧</span>Verifikasi dokumen</a><a class="sidebar-link active" href="<?= e(url_for('aktivitas.php')) ?>"><span>◷</span>Aktivitas</a><a class="sidebar-link" href="<?= e(url_for('readme.php')) ?>"><span>ⓘ</span>Panduan keamanan</a><div class="sidebar-tip"><span>✦</span><strong>Tips seller</strong><p>Log kunjungan membantu memantau perangkat yang mengakses dashboard.</p></div></aside>
<section class="panel-card customer-panel">

<div class="panel-top"><div><h2>Log kunjungan</h2><p>Dicatat otomatis dari header HTTP tiap kali halaman dibuka.</p></div><span class="secure-chip <?= is_patched() ? 'chip-safe' : 'chip-lab' ?>"><span></span><?= is_patched() ? 'Query aman' : 'Mode latihan' ?></span></div>
<div class="header-readout"><div><small>X-Forwarded-For (IP)</small><code><?= e($ip) ?></code></div><div><small>User-Agent (perangkat)</small><code><?= e(mb_strimwidth($ua, 0, 70, '…')) ?></code></div></div>
<?php if ($errorMessage !== ''): ?><div class="alert alert-error"><strong>Query log bermasalah</strong><p><?= e($errorMessage) ?></p><details><summary>Detail teknis</summary><code><?= e($connectionHint) ?></code></details></div>
<?php else: ?><div class="table-scroll"><table class="customer-table"><thead><tr><th>ID</th><th>WAKTU</th><th>IP</th><th>PERANGKAT</th></tr></thead><tbody><?php foreach ($logRows as $row): ?><tr><td><?= e($row['id']) ?></td><td><?= e($row['waktu']) ?></td><td><?= e($row['ip']) ?></td><td><?= e($row['perangkat']) ?></td></tr><?php endforeach; ?><?php if (!$logRows): ?><tr><td colspan="4" class="no-results">Belum ada log.</td></tr><?php endif; ?></tbody></table></div><?php endif; ?>
<details class="query-details"><summary><span>ⓘ</span> Lihat query INSERT yang digunakan</summary><div class="query-detail-content"><p><?php if (is_patched()): ?>Nilai header dikirim sebagai parameter prepared statement.<?php else: ?>Nilai header X-Forwarded-For dan User-Agent dirangkai langsung ke query INSERT.<?php endif; ?></p><pre><?= e($insertSql) ?></pre></div></details>

<hr class="panel-divider">

<div class="panel-top"><div><h2>Pelanggan di kotamu</h2><p>Kota favorit disimpan di <b>cookie</b>, lalu dipakai untuk memfilter pelanggan.</p></div></div>
<form class="customer-search" method="get" action="/aktivitas.php"><input type="hidden" name="mode" value="<?= e(app_mode()) ?>"><span class="search-icon">◍</span><input type="text" name="set_kota" value="<?= e($kota) ?>" placeholder="Simpan kota ke cookie, mis. Jakarta" autocomplete="off"><button type="submit">Simpan cookie</button><?php if ($kota !== ''): ?><a class="clear-search" href="<?= e(url_for('aktivitas.php', ['set_kota' => ''])) ?>">Hapus</a><?php endif; ?></form>
<p class="cookie-readout">Cookie saat ini: <code>kota=<?= e($kota === '' ? '(kosong)' : $kota) ?></code></p>
<?php if ($kota !== ''): ?>
<?php if ($kotaError !== ''): ?><div class="alert alert-error"><strong>Query filter bermasalah</strong><p><?= e($kotaError) ?></p></div>
<?php else: ?><div class="table-scroll"><table class="customer-table"><thead><tr><th>ID</th><th>NAMA</th><th>EMAIL</th><th>ALAMAT</th></tr></thead><tbody><?php foreach ($kotaRows as $row): ?><tr><td><?= e($row['id']) ?></td><td><?= e($row['nama']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['alamat']) ?></td></tr><?php endforeach; ?><?php if (!$kotaRows): ?><tr><td colspan="4" class="no-results">Tidak ada pelanggan untuk kota tersebut.</td></tr><?php endif; ?></tbody></table></div><?php endif; ?>
<details class="query-details"><summary><span>ⓘ</span> Lihat query SELECT yang digunakan</summary><div class="query-detail-content"><p><?php if (is_patched()): ?>Nilai cookie dikirim sebagai parameter prepared statement.<?php else: ?>Nilai cookie dirangkai langsung ke query SELECT.<?php endif; ?></p><pre><?= e($selectSql) ?></pre></div></details>
<?php endif; ?>

<?php if (!is_patched()): ?><div class="learning-note"><span>⚑</span><div><strong>Tujuan latihan — entry point non-form</strong><ul class="lab-list"><li><b>Header HTTP:</b> kirim header <code>X-Forwarded-For</code> berisi payload. Contoh exfiltrasi ke kolom perangkat:<br><code>X-Forwarded-For: x', (SELECT GROUP_CONCAT(nama,0x3a,email) FROM pelanggan))-- </code><br>Data pelanggan akan muncul di baris log. Uji dengan curl/Postman, bukan form.</li><li><b>Cookie:</b> ubah cookie <code>kota</code> lewat DevTools, mis. <code>Jakarta' OR '1'='1</code> untuk menampilkan semua pelanggan, atau UNION untuk kolom lain.</li></ul><p>Gunakan hanya pada lab lokal ini.</p></div><?php else: ?><div class="learning-note safe-note"><span>✓</span><div><strong>Perlindungan aktif</strong><p>Nilai dari header dan cookie dikirim sebagai parameter prepared statement, bukan dirangkai ke sintaks SQL. Semua input non-form diperlakukan sebagai data.</p></div></div><?php endif; ?>
</section></div>
<?php render_footer(); ?>
