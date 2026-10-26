<?php
require_once __DIR__ . '/../src/view.php';

$q = trim((string) ($_GET['q'] ?? ''));
$rows = [];
$sqlShown = '';
$errorMessage = '';
$connectionHint = '';
try {
    $db = lab_db();
    if ($q === '') {
        // Untuk lab, jangan tampilkan semua pelanggan secara default.
        // Query baru dijalankan setelah pengguna memasukkan kata kunci.
        $sqlShown = 'Belum ada query yang dijalankan. Masukkan kata kunci terlebih dahulu.';
        $rows = [];
    } elseif (is_patched()) {
        $sqlShown = 'SELECT id, nama, email, telepon, alamat FROM pelanggan WHERE nama LIKE :q OR email LIKE :q ORDER BY id';
        $stmt = $db->prepare($sqlShown);
        $stmt->execute(['q' => '%' . $q . '%']);
        $rows = $stmt->fetchAll();
    } else {
        // LAB ONLY: input q dirangkai langsung ke SQL untuk mendemonstrasikan SQL Injection.
        $sqlShown = "SELECT id, nama, email, telepon, alamat FROM pelanggan WHERE nama LIKE '%{$q}%' OR email LIKE '%{$q}%' ORDER BY id";
        $rows = $db->query($sqlShown)->fetchAll();
    }
} catch (Throwable $ex) {
    $errorMessage = db_error_hint($ex);
    $connectionHint = $ex->getMessage();
}
render_header('Data pelanggan', 'customers');
?>
<div class="page-intro"><div class="breadcrumbs"><a href="<?= e(url_for('index.php')) ?>">Beranda</a><span>/</span><span>Pusat Seller</span><span>/</span><strong>Data pelanggan</strong></div><div class="intro-row"><div><span class="eyebrow">PUSAT SELLER / CRM</span><h1>Data pelanggan</h1><p>Kelola dan temukan informasi pelanggan untuk membantu proses pesanan.</p></div><div class="intro-stat"><span class="stat-icon">♙</span><span><strong><?= $errorMessage === '' ? count($rows) : '—' ?></strong><small>Data ditampilkan</small></span></div></div></div>
<div class="customer-layout"><aside class="seller-sidebar"><div class="sidebar-user"><div class="seller-avatar">PK</div><div><strong>PasarKita Seller</strong><small>Demo workspace</small></div><span class="online-dot"></span></div><a class="sidebar-link active" href="<?= e(url_for('customers.php')) ?>"><span>▦</span>Data pelanggan</a><a class="sidebar-link" href="<?= e(url_for('upload.php')) ?>"><span>⇧</span>Verifikasi dokumen</a><a class="sidebar-link" href="<?= e(url_for('readme.php')) ?>"><span>ⓘ</span>Panduan keamanan</a><div class="sidebar-tip"><span>✦</span><strong>Tips seller</strong><p>Gunakan pencarian untuk menemukan pelanggan berdasarkan nama atau email.</p></div></aside>
<section class="panel-card customer-panel"><div class="panel-top"><div><h2>Direktori pelanggan</h2><p>Informasi kontak untuk alur pesanan demo.</p></div><span class="secure-chip <?= is_patched() ? 'chip-safe' : 'chip-lab' ?>"><span></span><?= is_patched() ? 'Query aman' : 'Mode latihan' ?></span></div><form class="customer-search" method="get" action="/customers.php"><input type="hidden" name="mode" value="<?= e(app_mode()) ?>"><label class="visually-hidden" for="customer-q">Cari pelanggan</label><span class="search-icon">⌕</span><input id="customer-q" type="text" name="q" value="<?= e($q) ?>" placeholder="Cari nama atau email pelanggan..." autocomplete="off"><button type="submit">Cari pelanggan</button><?php if ($q !== ''): ?><a class="clear-search" href="<?= e(url_for('customers.php')) ?>">Reset</a><?php endif; ?></form>
<?php if ($errorMessage !== ''): ?><div class="alert alert-error"><strong>Koneksi atau query bermasalah</strong><p><?= e($errorMessage) ?></p><details><summary>Detail teknis</summary><code><?= e($connectionHint) ?></code></details></div><?php else: ?><?php if ($q === ''): ?><div class="empty-search-state"><span class="empty-search-icon">⌕</span><strong>Mulai pencarian pelanggan</strong><p>Data pelanggan tidak ditampilkan otomatis. Masukkan nama atau email di kolom pencarian, lalu klik <b>Cari pelanggan</b>.</p><small>Dataset fiktif tersedia untuk latihan SQL Injection dan prepared statement.</small></div><?php else: ?><div class="table-meta"><span>Hasil untuk “<?= e($q) ?>”</span><span><?= count($rows) ?> data <span class="meta-divider">·</span> CSV latihan</span></div><div class="table-scroll"><table class="customer-table"><thead><tr><th>PELANGGAN</th><th>EMAIL</th><th>TELEPON</th><th>ALAMAT</th></tr></thead><tbody><?php foreach ($rows as $row): ?><tr><td><div class="customer-cell"><span class="customer-initial"><?= e(mb_substr((string) $row['nama'], 0, 1)) ?></span><span><strong><?= e($row['nama']) ?></strong><small>ID #<?= e($row['id']) ?></small></span></div></td><td><?= e($row['email']) ?></td><td><?= e($row['telepon']) ?></td><td><?= e($row['alamat']) ?></td></tr><?php endforeach; ?><?php if (!$rows): ?><tr><td colspan="4" class="no-results">Tidak ada pelanggan yang cocok dengan kata kunci tersebut.</td></tr><?php endif; ?></tbody></table></div><?php endif; ?><?php endif; ?>
<details class="query-details"><summary><span>ⓘ</span> Lihat query SQL yang digunakan</summary><div class="query-detail-content"><p><?php if ($q === ''): ?>Belum ada query pencarian yang dijalankan. Masukkan kata kunci terlebih dahulu.<?php elseif (is_patched()): ?>Mode patched menggunakan prepared statement. Nilai input dikirim sebagai parameter, bukan sebagai bagian dari sintaks SQL.<?php else: ?>Mode non-patched menggabungkan input q ke query SQL. Perubahan input dapat mengubah logika query.<?php endif; ?></p><pre><?= e($sqlShown) ?></pre><?php if (is_patched() && $q !== ''): ?><code class="parameter-note">Parameter :q = <?= e('%' . $q . '%') ?></code><?php endif; ?></div></details>
<?php if (!is_patched()): ?><div class="learning-note"><span>⚑</span><div><strong>Tujuan latihan</strong><p>Coba kata kunci biasa seperti <code>Andi</code>, lalu bandingkan dengan input yang mengubah kondisi query. Gunakan hanya pada lab lokal ini.</p></div></div><?php else: ?><div class="learning-note safe-note"><span>✓</span><div><strong>Perlindungan aktif</strong><p>Prepared statement memisahkan input dari sintaks SQL. Input yang tampak seperti SQL akan diperlakukan sebagai teks pencarian biasa.</p></div></div><?php endif; ?></section></div>
<?php render_footer(); ?>
