<?php
require_once __DIR__ . '/../src/view.php';

$q = trim((string) ($_GET['q'] ?? ''));
<<<<<<< HEAD
$urut = trim((string) ($_GET['urut'] ?? ''));
$id = trim((string) ($_GET['id'] ?? ''));

=======
>>>>>>> b48ca0604436dcaee5b758caf6836d98ca6253df
$rows = [];
$sqlShown = '';
$errorMessage = '';
$connectionHint = '';
<<<<<<< HEAD

// Detail pelanggan berdasarkan id (demonstrasi SQLi numerik / UNION).
$detailRow = null;
$detailRowsAll = [];
$detailSql = '';
$detailError = '';

try {
    $db = lab_db();

    // ----- Pencarian pelanggan (LIKE) + pengurutan (ORDER BY) -----
    if ($q !== '') {
        if (is_patched()) {
            // Pengurutan: prepared statement tidak bisa mem-parameter-kan nama kolom,
            // jadi perlindungan yang benar adalah whitelist (allow-list).
            $kolomDiizinkan = ['id', 'nama', 'email'];
            $orderBy = in_array($urut, $kolomDiizinkan, true) ? $urut : 'id';
            $sqlShown = "SELECT id, nama, email, telepon, alamat FROM pelanggan WHERE nama LIKE :q1 OR email LIKE :q2 ORDER BY {$orderBy}";
            $stmt = $db->prepare($sqlShown);
            $stmt->execute(['q1' => '%' . $q . '%', 'q2' => '%' . $q . '%']);
            $rows = $stmt->fetchAll();
        } else {
            // LAB ONLY: input q dan urut dirangkai langsung ke SQL (SQL Injection).
            $orderBy = $urut !== '' ? $urut : 'id';
            $sqlShown = "SELECT id, nama, email, telepon, alamat FROM pelanggan WHERE nama LIKE '%{$q}%' OR email LIKE '%{$q}%' ORDER BY {$orderBy}";
            $rows = $db->query($sqlShown)->fetchAll();
        }
    } else {
        // Untuk lab, jangan tampilkan semua pelanggan secara default.
        $sqlShown = 'Belum ada query yang dijalankan. Masukkan kata kunci terlebih dahulu.';
        $rows = [];
    }

    // ----- Detail pelanggan berdasarkan id (parameter numerik) -----
    if ($id !== '') {
        if (is_patched()) {
            // Input dipaksa menjadi integer lalu dikirim sebagai parameter.
            $detailSql = 'SELECT id, nama, email, telepon, alamat FROM pelanggan WHERE id = :id';
            $stmt = $db->prepare($detailSql);
            $stmt->execute(['id' => (int) $id]);
            $detailRowsAll = $stmt->fetchAll();
        } else {
            // LAB ONLY: id dirangkai tanpa tanda kutip -> injeksi numerik & UNION.
            $detailSql = "SELECT id, nama, email, telepon, alamat FROM pelanggan WHERE id = {$id}";
            $detailRowsAll = $db->query($detailSql)->fetchAll();
        }
        $detailRow = $detailRowsAll[0] ?? null;
=======
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
>>>>>>> b48ca0604436dcaee5b758caf6836d98ca6253df
    }
} catch (Throwable $ex) {
    $errorMessage = db_error_hint($ex);
    $connectionHint = $ex->getMessage();
<<<<<<< HEAD
    $detailError = $ex->getMessage();
}

// Helper untuk membuat tautan sort yang mempertahankan q, mode, dan id.
$sortLink = static function (string $kolom) use ($q, $id): string {
    $params = ['q' => $q, 'urut' => $kolom];
    if ($id !== '') {
        $params['id'] = $id;
    }
    return e(url_for('customers.php', $params));
};

render_header('Data pelanggan', 'customers');
?>
<div class="page-intro"><div class="breadcrumbs"><a href="<?= e(url_for('index.php')) ?>">Beranda</a><span>/</span><span>Pusat Seller</span><span>/</span><strong>Data pelanggan</strong></div><div class="intro-row"><div><span class="eyebrow">PUSAT SELLER / CRM</span><h1>Data pelanggan</h1><p>Kelola dan temukan informasi pelanggan untuk membantu proses pesanan.</p></div><div class="intro-stat"><span class="stat-icon">♙</span><span><strong><?= $errorMessage === '' ? count($rows) : '—' ?></strong><small>Data ditampilkan</small></span></div></div></div>
<div class="customer-layout"><aside class="seller-sidebar"><div class="sidebar-user"><div class="seller-avatar">PK</div><div><strong>PasarKita Seller</strong><small>Demo workspace</small></div><span class="online-dot"></span></div><a class="sidebar-link active" href="<?= e(url_for('customers.php')) ?>"><span>▦</span>Data pelanggan</a><a class="sidebar-link" href="<?= e(url_for('upload.php')) ?>"><span>⇧</span>Verifikasi dokumen</a><a class="sidebar-link" href="<?= e(url_for('aktivitas.php')) ?>"><span>◷</span>Aktivitas</a><a class="sidebar-link" href="<?= e(url_for('readme.php')) ?>"><span>ⓘ</span>Panduan keamanan</a><div class="sidebar-tip"><span>✦</span><strong>Tips seller</strong><p>Gunakan pencarian untuk menemukan pelanggan berdasarkan nama atau email.</p></div></aside>
<section class="panel-card customer-panel"><div class="panel-top"><div><h2>Direktori pelanggan</h2><p>Informasi kontak untuk alur pesanan demo.</p></div><span class="secure-chip <?= is_patched() ? 'chip-safe' : 'chip-lab' ?>"><span></span><?= is_patched() ? 'Query aman' : 'Mode latihan' ?></span></div><form class="customer-search" method="get" action="/customers.php"><input type="hidden" name="mode" value="<?= e(app_mode()) ?>"><?php if ($urut !== ''): ?><input type="hidden" name="urut" value="<?= e($urut) ?>"><?php endif; ?><label class="visually-hidden" for="customer-q">Cari pelanggan</label><span class="search-icon">⌕</span><input id="customer-q" type="text" name="q" value="<?= e($q) ?>" placeholder="Cari nama atau email pelanggan..." autocomplete="off"><button type="submit">Cari pelanggan</button><?php if ($q !== ''): ?><a class="clear-search" href="<?= e(url_for('customers.php')) ?>">Reset</a><?php endif; ?></form>
<?php if ($errorMessage !== ''): ?><div class="alert alert-error"><strong>Koneksi atau query bermasalah</strong><p><?= e($errorMessage) ?></p><details><summary>Detail teknis</summary><code><?= e($connectionHint) ?></code></details></div><?php else: ?><?php if ($q === ''): ?><div class="empty-search-state"><span class="empty-search-icon">⌕</span><strong>Mulai pencarian pelanggan</strong><p>Data pelanggan tidak ditampilkan otomatis. Masukkan nama atau email di kolom pencarian, lalu klik <b>Cari pelanggan</b>.</p><small>Dataset fiktif tersedia untuk latihan SQL Injection dan prepared statement.</small></div><?php else: ?><div class="table-meta"><span>Hasil untuk “<?= e($q) ?>”</span><span><?= count($rows) ?> data <span class="meta-divider">·</span> CSV latihan</span></div><div class="sort-bar"><span class="sort-label">Urutkan:</span><a href="<?= $sortLink('id') ?>"><?= $urut === 'id' || $urut === '' ? '●' : '○' ?> ID</a><a href="<?= $sortLink('nama') ?>"><?= $urut === 'nama' ? '●' : '○' ?> Nama</a><a href="<?= $sortLink('email') ?>"><?= $urut === 'email' ? '●' : '○' ?> Email</a></div><div class="table-scroll"><table class="customer-table"><thead><tr><th>PELANGGAN</th><th>EMAIL</th><th>TELEPON</th><th>ALAMAT</th><th>AKSI</th></tr></thead><tbody><?php foreach ($rows as $row): ?><tr><td><div class="customer-cell"><span class="customer-initial"><?= e(mb_substr((string) $row['nama'], 0, 1)) ?></span><span><strong><?= e($row['nama']) ?></strong><small>ID #<?= e($row['id']) ?></small></span></div></td><td><?= e($row['email']) ?></td><td><?= e($row['telepon']) ?></td><td><?= e($row['alamat']) ?></td><td><a class="row-detail-link" href="<?= e(url_for('customers.php', ['q' => $q, 'urut' => $urut, 'id' => (string) $row['id']])) ?>">Detail</a></td></tr><?php endforeach; ?><?php if (!$rows): ?><tr><td colspan="5" class="no-results">Tidak ada pelanggan yang cocok dengan kata kunci tersebut.</td></tr><?php endif; ?></tbody></table></div><?php endif; ?><?php endif; ?>
<details class="query-details"><summary><span>ⓘ</span> Lihat query SQL yang digunakan</summary><div class="query-detail-content"><p><?php if ($q === ''): ?>Belum ada query pencarian yang dijalankan. Masukkan kata kunci terlebih dahulu.<?php elseif (is_patched()): ?>Mode patched menggunakan prepared statement untuk nilai, dan whitelist untuk nama kolom pengurutan.<?php else: ?>Mode non-patched menggabungkan input q dan urut ke query SQL. Perubahan input dapat mengubah logika query.<?php endif; ?></p><pre><?= e($sqlShown) ?></pre><?php if (is_patched() && $q !== ''): ?><code class="parameter-note">Parameter :q = <?= e('%' . $q . '%') ?></code><?php endif; ?></div></details>

<?php if ($id !== ''): ?>
<div class="detail-block">
<div class="detail-block-head"><h2>Detail pelanggan</h2><a class="clear-search" href="<?= e(url_for('customers.php', ['q' => $q, 'urut' => $urut])) ?>">Tutup detail</a></div>
<?php if ($detailError !== ''): ?><div class="alert alert-error"><strong>Query detail bermasalah</strong><p><?= e($detailError) ?></p></div>
<?php elseif ($detailRowsAll): ?><div class="table-scroll"><table class="customer-table"><thead><tr><th>ID</th><th>NAMA</th><th>EMAIL</th><th>TELEPON</th><th>ALAMAT</th></tr></thead><tbody><?php foreach ($detailRowsAll as $d): ?><tr><td><?= e($d['id']) ?></td><td><?= e($d['nama']) ?></td><td><?= e($d['email']) ?></td><td><?= e($d['telepon']) ?></td><td><?= e($d['alamat']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php if (count($detailRowsAll) > 1): ?><small class="detail-hint">Query ini seharusnya mengembalikan satu baris. <?= count($detailRowsAll) ?> baris muncul karena kondisi query berubah.</small><?php endif; ?>
<?php else: ?><p class="no-results">Tidak ada pelanggan dengan ID tersebut.</p><?php endif; ?>
<details class="query-details"><summary><span>ⓘ</span> Lihat query detail</summary><div class="query-detail-content"><p><?php if (is_patched()): ?>Input id dipaksa menjadi integer dan dikirim sebagai parameter.<?php else: ?>Input id dirangkai langsung ke query tanpa tanda kutip, sehingga rentan injeksi numerik dan UNION.<?php endif; ?></p><pre><?= e($detailSql) ?></pre><?php if (is_patched()): ?><code class="parameter-note">Parameter :id = <?= e((string) (int) $id) ?></code><?php endif; ?></div></details>
</div>
<?php endif; ?>

<?php if (!is_patched()): ?><div class="learning-note"><span>⚑</span><div><strong>Tujuan latihan</strong><p>Tiga titik SQL injection pada mode non-patched:</p><ul class="lab-list"><li><b>Pencarian (LIKE):</b> input <code>q</code> seperti <code>' OR 1=1 -- </code> mengubah kondisi.</li><li><b>Pengurutan (ORDER BY):</b> ubah parameter <code>urut</code> di URL, mis. <code>urut=(CASE WHEN 1=1 THEN nama ELSE email END)</code> atau <code>urut=1</code>.</li><li><b>Detail (numerik / UNION):</b> ubah <code>id</code> di URL, mis. <code>id=1 OR 1=1</code> atau <code>id=0 UNION SELECT 1,nama,email,telepon,alamat FROM pelanggan</code>.</li></ul><p>Gunakan hanya pada lab lokal ini.</p></div><?php else: ?><div class="learning-note safe-note"><span>✓</span><div><strong>Perlindungan aktif</strong><p>Nilai input memakai prepared statement, nama kolom pengurutan memakai whitelist, dan id dipaksa menjadi integer. Input yang tampak seperti SQL diperlakukan sebagai data biasa.</p></div></div><?php endif; ?></section></div>
=======
}
render_header('Data pelanggan', 'customers');
?>
<div class="page-intro"><div class="breadcrumbs"><a href="<?= e(url_for('index.php')) ?>">Beranda</a><span>/</span><span>Pusat Seller</span><span>/</span><strong>Data pelanggan</strong></div><div class="intro-row"><div><span class="eyebrow">PUSAT SELLER / CRM</span><h1>Data pelanggan</h1><p>Kelola dan temukan informasi pelanggan untuk membantu proses pesanan.</p></div><div class="intro-stat"><span class="stat-icon">♙</span><span><strong><?= $errorMessage === '' ? count($rows) : '—' ?></strong><small>Data ditampilkan</small></span></div></div></div>
<div class="customer-layout"><aside class="seller-sidebar"><div class="sidebar-user"><div class="seller-avatar">PK</div><div><strong>PasarKita Seller</strong><small>Demo workspace</small></div><span class="online-dot"></span></div><a class="sidebar-link active" href="<?= e(url_for('customers.php')) ?>"><span>▦</span>Data pelanggan</a><a class="sidebar-link" href="<?= e(url_for('upload.php')) ?>"><span>⇧</span>Verifikasi dokumen</a><a class="sidebar-link" href="<?= e(url_for('readme.php')) ?>"><span>ⓘ</span>Panduan keamanan</a><div class="sidebar-tip"><span>✦</span><strong>Tips seller</strong><p>Gunakan pencarian untuk menemukan pelanggan berdasarkan nama atau email.</p></div></aside>
<section class="panel-card customer-panel"><div class="panel-top"><div><h2>Direktori pelanggan</h2><p>Informasi kontak untuk alur pesanan demo.</p></div><span class="secure-chip <?= is_patched() ? 'chip-safe' : 'chip-lab' ?>"><span></span><?= is_patched() ? 'Query aman' : 'Mode latihan' ?></span></div><form class="customer-search" method="get" action="/customers.php"><input type="hidden" name="mode" value="<?= e(app_mode()) ?>"><label class="visually-hidden" for="customer-q">Cari pelanggan</label><span class="search-icon">⌕</span><input id="customer-q" type="text" name="q" value="<?= e($q) ?>" placeholder="Cari nama atau email pelanggan..." autocomplete="off"><button type="submit">Cari pelanggan</button><?php if ($q !== ''): ?><a class="clear-search" href="<?= e(url_for('customers.php')) ?>">Reset</a><?php endif; ?></form>
<?php if ($errorMessage !== ''): ?><div class="alert alert-error"><strong>Koneksi atau query bermasalah</strong><p><?= e($errorMessage) ?></p><details><summary>Detail teknis</summary><code><?= e($connectionHint) ?></code></details></div><?php else: ?><?php if ($q === ''): ?><div class="empty-search-state"><span class="empty-search-icon">⌕</span><strong>Mulai pencarian pelanggan</strong><p>Data pelanggan tidak ditampilkan otomatis. Masukkan nama atau email di kolom pencarian, lalu klik <b>Cari pelanggan</b>.</p><small>Dataset fiktif tersedia untuk latihan SQL Injection dan prepared statement.</small></div><?php else: ?><div class="table-meta"><span>Hasil untuk “<?= e($q) ?>”</span><span><?= count($rows) ?> data <span class="meta-divider">·</span> CSV latihan</span></div><div class="table-scroll"><table class="customer-table"><thead><tr><th>PELANGGAN</th><th>EMAIL</th><th>TELEPON</th><th>ALAMAT</th></tr></thead><tbody><?php foreach ($rows as $row): ?><tr><td><div class="customer-cell"><span class="customer-initial"><?= e(mb_substr((string) $row['nama'], 0, 1)) ?></span><span><strong><?= e($row['nama']) ?></strong><small>ID #<?= e($row['id']) ?></small></span></div></td><td><?= e($row['email']) ?></td><td><?= e($row['telepon']) ?></td><td><?= e($row['alamat']) ?></td></tr><?php endforeach; ?><?php if (!$rows): ?><tr><td colspan="4" class="no-results">Tidak ada pelanggan yang cocok dengan kata kunci tersebut.</td></tr><?php endif; ?></tbody></table></div><?php endif; ?><?php endif; ?>
<details class="query-details"><summary><span>ⓘ</span> Lihat query SQL yang digunakan</summary><div class="query-detail-content"><p><?php if ($q === ''): ?>Belum ada query pencarian yang dijalankan. Masukkan kata kunci terlebih dahulu.<?php elseif (is_patched()): ?>Mode patched menggunakan prepared statement. Nilai input dikirim sebagai parameter, bukan sebagai bagian dari sintaks SQL.<?php else: ?>Mode non-patched menggabungkan input q ke query SQL. Perubahan input dapat mengubah logika query.<?php endif; ?></p><pre><?= e($sqlShown) ?></pre><?php if (is_patched() && $q !== ''): ?><code class="parameter-note">Parameter :q = <?= e('%' . $q . '%') ?></code><?php endif; ?></div></details>
<?php if (!is_patched()): ?><div class="learning-note"><span>⚑</span><div><strong>Tujuan latihan</strong><p>Coba kata kunci biasa seperti <code>Andi</code>, lalu bandingkan dengan input yang mengubah kondisi query. Gunakan hanya pada lab lokal ini.</p></div></div><?php else: ?><div class="learning-note safe-note"><span>✓</span><div><strong>Perlindungan aktif</strong><p>Prepared statement memisahkan input dari sintaks SQL. Input yang tampak seperti SQL akan diperlakukan sebagai teks pencarian biasa.</p></div></div><?php endif; ?></section></div>
>>>>>>> b48ca0604436dcaee5b758caf6836d98ca6253df
<?php render_footer(); ?>
