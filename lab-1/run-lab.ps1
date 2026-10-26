$ErrorActionPreference = 'Stop'
$php = 'C:\xampp\php\php.exe'
if (-not (Test-Path $php)) {
    Write-Host 'Tidak menemukan PHP XAMPP di C:\xampp\php\php.exe.' -ForegroundColor Red
    Write-Host 'Ubah variabel $php pada run-lab.ps1 sesuai lokasi instalasi XAMPP kamu.'
    exit 1
}
$modules = & $php -m
if (-not ($modules -match '(?im)^pdo_mysql$')) {
    Write-Host 'Ekstensi pdo_mysql belum aktif. Periksa php.ini dan aktifkan extension=pdo_mysql.' -ForegroundColor Red
    exit 1
}
Set-Location $PSScriptRoot
Write-Host 'PasarKita Security Lab: http://127.0.0.1:8080' -ForegroundColor Green
Write-Host 'Pastikan service MySQL/MariaDB aktif. Server terikat ke localhost saja.'
Write-Host 'Tekan Ctrl+C untuk menghentikan server PHP.'
& $php -S 127.0.0.1:8080 -t "$PSScriptRoot\public"
