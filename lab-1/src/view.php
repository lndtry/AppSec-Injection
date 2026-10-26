<?php
require_once __DIR__ . '/bootstrap.php';

function render_header(string $pageTitle, string $active = ''): void
{
    $patched = is_patched();
    $search = (string) ($_GET['search'] ?? '');
    ?>
    <!doctype html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f7f8fc">
        <title><?= e($pageTitle) ?> | PasarKita</title>
        <link rel="stylesheet" href="/style.css">
    </head>
    <body>
    <div class="announcement"><span class="announcement-dot"></span> Promo akhir bulan · Banyak pilihan, satu tempat <span class="announcement-right">Belanja nyaman bersama PasarKita</span></div>
    <header class="site-header">
        <a class="brand" href="<?= e(url_for('index.php')) ?>" aria-label="PasarKita Beranda"><span class="brand-mark">p</span><span>pasarkita<span class="brand-dot">.</span><small>marketplace lokal</small></span></a>
        <form class="global-search" action="/index.php" method="get">
            <input type="hidden" name="mode" value="<?= e(app_mode()) ?>">
            <span class="search-icon" aria-hidden="true">⌕</span>
            <input type="search" name="search" value="<?= e($search) ?>" placeholder="Cari barang favoritmu..." aria-label="Cari produk">
            <button type="submit">Cari</button>
        </form>
        <div class="header-tools">
            <a class="header-action" href="<?= e(url_for('index.php', ['section' => 'favorit'])) ?>"><span class="action-icon">♡</span><span>Favorit</span></a>
            <a class="header-action cart-action" href="<?= e(url_for('index.php', ['section' => 'keranjang'])) ?>"><span class="action-icon">▢</span><span>Keranjang</span><b>0</b></a>
        </div>
    </header>
    <nav class="main-nav">
        <div class="nav-links">
            <a class="<?= $active === 'home' ? 'active' : '' ?>" href="<?= e(url_for('index.php')) ?>">Beranda</a>
            <a href="<?= e(url_for('index.php', ['search' => '']) ) ?>#produk">Kategori</a>
            <a href="<?= e(url_for('index.php')) ?>#promo">Promo</a>
            <details class="nav-dropdown" <?= in_array($active, ['customers', 'upload', 'aktivitas', 'guide'], true) ? 'open' : '' ?>>
                <summary class="<?= in_array($active, ['customers', 'upload', 'aktivitas', 'guide'], true) ? 'active' : '' ?>">Pusat Seller <span class="chevron">⌄</span></summary>
                <div class="dropdown-menu">
                    <a href="<?= e(url_for('customers.php')) ?>"><span>♙</span><span>Data pelanggan<small>CRM & pencarian pelanggan</small></span></a>
                    <a href="<?= e(url_for('upload.php')) ?>"><span>⇧</span><span>Verifikasi dokumen<small>Unggah bukti / dokumen seller</small></span></a>
                    <a href="<?= e(url_for('aktivitas.php')) ?>"><span>◷</span><span>Aktivitas<small>Log kunjungan & filter kota</small></span></a>
                    <a href="<?= e(url_for('readme.php')) ?>"><span>ⓘ</span><span>Panduan & keamanan<small>Pelajari dua mode aplikasi</small></span></a>
                </div>
            </details>
        </div>
        <div class="mode-control"><span class="mode-label">Mode aplikasi</span><div class="mode-switch" role="group" aria-label="Pilih versi aplikasi"><a class="mode-option <?= !$patched ? 'selected vulnerable' : '' ?>" href="<?= e(mode_url('non-patched')) ?>">Non-patched</a><a class="mode-option <?= $patched ? 'selected patched' : '' ?>" href="<?= e(mode_url('patched')) ?>">Patched</a></div></div>
    </nav>
    <div class="mode-notice <?= $patched ? 'notice-safe' : 'notice-vulnerable' ?>"><span class="notice-symbol"><?= $patched ? '✓' : '!' ?></span><div><strong><?= $patched ? 'Versi patched aktif' : 'Versi non-patched aktif' ?></strong><span><?= $patched ? 'Perlindungan diperbaiki: query berparameter dan upload tervalidasi.' : 'Mode latihan lokal: dua kelemahan sengaja dibiarkan aktif untuk pembelajaran.' ?></span></div><a href="<?= e(url_for('readme.php')) ?>">Pelajari perbedaannya <span aria-hidden="true">→</span></a></div>
    <main class="page-shell">
    <?php
}

function render_footer(): void
{
    ?>
    </main>
    <footer class="site-footer"><div class="footer-brand"><span class="brand-mark">p</span><div><strong>pasarkita<span class="brand-dot">.</span></strong><small>marketplace lokal</small></div></div><div class="footer-copy"><strong>Belanja lebih mudah, setiap hari.</strong><span>Demo edukasi keamanan web · Seluruh produk dan pelanggan merupakan data fiktif.</span></div><div class="footer-right"><span class="local-only"><span></span> Lab lokal</span><span>© 2026 PasarKita</span></div></footer>
    </body>
    </html>
    <?php
}
