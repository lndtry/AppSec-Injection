<?php
const DB_HOST = '127.0.0.1';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'lab_app';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('<pre style="padding:20px;color:#c4cad3;background:#06070a;font-family:monospace">'
        . "DB connection failed: " . htmlspecialchars($conn->connect_error)
        . "\n\nImport db.sql dulu lewat phpMyAdmin (http://localhost/phpmyadmin).</pre>");
}
$conn->set_charset('utf8mb4');

$docroot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
$appdir  = str_replace('\\', '/', realpath(__DIR__));
$rel     = ($docroot && strpos($appdir, $docroot) === 0) ? substr($appdir, strlen($docroot)) : '';
define('APP_BASE', rtrim('/' . ltrim($rel, '/'), '/'));
