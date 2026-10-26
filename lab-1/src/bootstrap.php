<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_mode(): string
{
    $mode = (string) ($_GET['mode'] ?? 'non-patched');
    return in_array($mode, ['patched', 'non-patched'], true) ? $mode : 'non-patched';
}

function is_patched(): bool
{
    return app_mode() === 'patched';
}

function url_for(string $page, array $params = []): string
{
    $params = array_merge(['mode' => app_mode()], $params);
    return '/' . ltrim($page, '/') . '?' . http_build_query($params);
}

function mode_url(string $mode): string
{
    $page = basename((string) ($_SERVER['PHP_SELF'] ?? 'index.php'));
    $params = $_GET;
    $params['mode'] = in_array($mode, ['patched', 'non-patched'], true) ? $mode : 'non-patched';
    return '/' . $page . '?' . http_build_query($params);
}

/**
 * Pada Windows/XAMPP gunakan CSV di folder data project.
 * Pada Linux, file /root/data_pelanggan.csv dipakai hanya jika memang bisa dibaca.
 * Jangan jalankan server PHP sebagai root hanya untuk membaca file ini.
 */
function customer_csv_path(): string
{
    $rootFile = '/root/data_pelanggan.csv';
    if (DIRECTORY_SEPARATOR !== "\\" && is_readable($rootFile)) {
        return $rootFile;
    }
    return __DIR__ . '/../data/data_pelanggan.csv';
}

function lab_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $serverDsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', DB_HOST, DB_PORT);
    $server = new PDO($serverDsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $safeDbName = str_replace('`', '``', DB_NAME);
    $server->exec("CREATE DATABASE IF NOT EXISTS `{$safeDbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    unset($server);

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS pelanggan (
            id INT NOT NULL PRIMARY KEY,
            nama VARCHAR(120) NOT NULL,
            email VARCHAR(255) NOT NULL,
            telepon VARCHAR(50) NOT NULL,
            alamat VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $count = (int) $pdo->query('SELECT COUNT(*) FROM pelanggan')->fetchColumn();
    $csv = customer_csv_path();
    if ($count === 0 && is_readable($csv)) {
        $handle = fopen($csv, 'r');
        if ($handle !== false) {
            fgetcsv($handle); // Lewati header CSV.
            $insert = $pdo->prepare(
                'INSERT INTO pelanggan (id, nama, email, telepon, alamat) VALUES (?, ?, ?, ?, ?)'
            );
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 5) {
                    $insert->execute(array_slice($row, 0, 5));
                }
            }
            fclose($handle);
        }
    }

    return $pdo;
}

function db_error_hint(Throwable $exception): string
{
    $message = $exception->getMessage();
    if (str_contains($message, 'actively refused') || str_contains($message, 'Connection refused') || str_contains($message, '[2002]')) {
        return 'Database belum bisa dihubungi. Buka XAMPP Control Panel, Start MySQL, lalu pastikan DB_PORT di src/config.php sama dengan port MySQL (umumnya 3306).';
    }
    if (str_contains($message, 'could not find driver')) {
        return 'PHP belum memuat driver pdo_mysql. Periksa php.ini dan aktifkan extension=pdo_mysql, lalu restart server PHP.';
    }
    return 'Koneksi/database bermasalah. Periksa pengaturan di src/config.php dan pastikan MySQL/MariaDB aktif.';
}
