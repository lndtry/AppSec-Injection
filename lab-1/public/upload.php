<?php
require_once __DIR__ . '/../src/view.php';

$message = '';
$messageType = '';
$storedName = '';
$originalName = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['document']) || !is_array($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Unggahan gagal. Pilih file yang valid dan coba lagi.';
        $messageType = 'error';
    } else {
        $originalName = basename((string) $_FILES['document']['name']);
        $tmpName = (string) $_FILES['document']['tmp_name'];
        if (is_patched()) {
            $allowed = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'pdf' => 'application/pdf',
            ];
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpName) ?: 'application/octet-stream';
            if (!isset($allowed[$extension]) || $allowed[$extension] !== $mime) {
                $message = 'File ditolak. Versi patched hanya menerima JPG, PNG, atau PDF dengan tipe konten yang sesuai.';
                $messageType = 'error';
            } else {
                $storage = dirname(__DIR__) . '/storage/uploads';
                if (!is_dir($storage)) {
                    mkdir($storage, 0750, true);
                }
                $storedName = bin2hex(random_bytes(12)) . '.' . $extension;
                if (move_uploaded_file($tmpName, $storage . '/' . $storedName)) {
                    $message = 'Dokumen berhasil diterima. Nama file diacak dan berkas disimpan di luar webroot; file tidak dapat dieksekusi melalui URL.';
                    $messageType = 'success';
                } else {
                    $message = 'Dokumen belum dapat disimpan. Periksa izin folder storage/uploads.';
                    $messageType = 'error';
                }
            }
        } else {
            // LAB ONLY: tidak ada pemeriksaan ekstensi/MIME/konten; PHP di uploads dapat dieksekusi.
            $safeName = basename($originalName);
            $destination = __DIR__ . '/uploads/' . $safeName;
            if (move_uploaded_file($tmpName, $destination)) {
                $message = 'File berhasil diunggah tanpa validasi tipe. Dalam mode ini, file PHP pada folder web dapat dieksekusi oleh server lab.';
                $messageType = 'success';
                $storedName = $safeName;
            } else {
                $message = 'File tidak berhasil disimpan. Periksa izin folder public/uploads.';
                $messageType = 'error';
            }
        }
    }
}

$publicFiles = array_values(array_filter(scandir(__DIR__ . '/uploads') ?: [], static fn($f) => !in_array($f, ['.', '..', '.gitkeep'], true) && is_file(__DIR__ . '/uploads/' . $f)));
$privateFiles = array_values(array_filter(scandir(dirname(__DIR__) . '/storage/uploads') ?: [], static fn($f) => !in_array($f, ['.', '..', '.gitkeep'], true) && is_file(dirname(__DIR__) . '/storage/uploads/' . $f)));
render_header('Verifikasi dokumen', 'upload');
?>
<div class="page-intro"><div class="breadcrumbs"><a href="<?= e(url_for('index.php')) ?>">Beranda</a><span>/</span><span>Pusat Seller</span><span>/</span><strong>Verifikasi dokumen</strong></div><div class="intro-row"><div><span class="eyebrow">PUSAT SELLER / VERIFIKASI</span><h1>Verifikasi dokumen</h1><p>Unggah dokumen pendukung untuk membantu proses verifikasi akun seller.</p></div><div class="intro-stat"><span class="stat-icon">⇧</span><span><strong><?= is_patched() ? count($privateFiles) : count($publicFiles) ?></strong><small>Dokumen diterima</small></span></div></div></div>
<div class="customer-layout"><aside class="seller-sidebar"><div class="sidebar-user"><div class="seller-avatar">PK</div><div><strong>PasarKita Seller</strong><small>Demo workspace</small></div><span class="online-dot"></span></div><a class="sidebar-link" href="<?= e(url_for('customers.php')) ?>"><span>▦</span>Data pelanggan</a><a class="sidebar-link active" href="<?= e(url_for('upload.php')) ?>"><span>⇧</span>Verifikasi dokumen</a><a class="sidebar-link" href="<?= e(url_for('aktivitas.php')) ?>"><span>◷</span>Aktivitas</a><a class="sidebar-link" href="<?= e(url_for('readme.php')) ?>"><span>ⓘ</span>Panduan keamanan</a><div class="sidebar-tip"><span>✦</span><strong>Dokumen yang diperlukan</strong><p><?= is_patched() ? 'Format yang diterima: JPG, PNG, atau PDF.' : 'Mode latihan tidak memvalidasi tipe file.' ?></p></div></aside>
<section class="panel-card upload-panel"><div class="panel-top"><div><h2>Dokumen seller</h2><p>Pastikan dokumen yang kamu pilih sesuai kebutuhan verifikasi.</p></div><span class="secure-chip <?= is_patched() ? 'chip-safe' : 'chip-lab' ?>"><span></span><?= is_patched() ? 'Upload aman' : 'Mode latihan' ?></span></div>
<?php if ($message !== ''): ?><div class="alert <?= $messageType === 'success' ? 'alert-success' : 'alert-error' ?>"><strong><?= $messageType === 'success' ? 'Status unggahan' : 'Unggahan belum berhasil' ?></strong><p><?= e($message) ?></p><?php if (!is_patched() && $messageType === 'success' && strtolower(pathinfo($storedName, PATHINFO_EXTENSION)) === 'php'): ?><p><a class="inline-alert-link" href="/uploads/<?= rawurlencode($storedName) ?>" target="_blank" rel="noopener">Buka file PHP yang diunggah ↗</a></p><?php endif; ?></div><?php endif; ?>
<form class="upload-dropzone" method="post" enctype="multipart/form-data"><input type="file" id="document" name="document" <?= is_patched() ? 'accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf"' : '' ?> required><label for="document" class="dropzone-label"><span class="upload-cloud">⇧</span><strong>Pilih file untuk diunggah</strong><small><?= is_patched() ? 'JPG, PNG, PDF · File disimpan di luar webroot' : 'Mode latihan: tipe dan ekstensi file tidak diperiksa' ?></small><span class="btn-secondary">Pilih dokumen</span></label><button class="btn-primary upload-submit" type="submit">Unggah dokumen <span>→</span></button></form>
<div class="upload-explain <?= is_patched() ? 'explain-safe' : 'explain-lab' ?>"><span class="explain-icon"><?= is_patched() ? '✓' : '!' ?></span><div><strong><?= is_patched() ? 'Validasi dan penyimpanan aman aktif' : 'Pemeriksaan tipe file sengaja dinonaktifkan' ?></strong><p><?php if (is_patched()): ?>Aplikasi memeriksa ekstensi dan MIME, memberi nama acak, menyimpan dokumen di luar direktori publik, dan tidak menyediakan URL eksekusi file.<?php else: ?>File disimpan dengan nama aslinya di direktori publik uploads. Dalam server lab lokal, file PHP yang diunggah dapat berjalan sebagai kode PHP. Jangan gunakan di sistem publik.<?php endif; ?></p></div></div>
<div class="recent-files"><div class="recent-heading"><h3>Dokumen terbaru</h3><span><?= is_patched() ? count($privateFiles) : count($publicFiles) ?> file</span></div><?php $shownFiles = is_patched() ? $privateFiles : $publicFiles; if (!$shownFiles): ?><div class="empty-files"><span>▱</span><p>Belum ada dokumen yang diunggah.</p></div><?php else: ?><ul><?php foreach (array_slice(array_reverse($shownFiles), 0, 8) as $file): ?><li><span class="file-icon">▤</span><span><strong><?= e($file) ?></strong><small><?= is_patched() ? 'Disimpan di luar webroot' : 'Disimpan di direktori publik' ?></small></span><?php if (!is_patched()): ?><a href="/uploads/<?= rawurlencode($file) ?>" target="_blank" rel="noopener">Buka ↗</a><?php else: ?><span class="private-tag">Privat</span><?php endif; ?></li><?php endforeach; ?></ul><?php endif; ?></div>
<?php if (!is_patched()): ?><div class="payload-hint"><strong>File demo untuk pengujian</strong><p>Di folder project terdapat <code>payloads/command-demo.php</code>. Dalam lab lokal, file tersebut bisa digunakan untuk melihat dampak unggahan PHP yang tidak divalidasi. Payload demo hanya menjalankan perintah identitas tetap.</p></div><?php endif; ?></section></div>
<?php render_footer(); ?>
