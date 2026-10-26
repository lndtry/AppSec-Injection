<?php
require __DIR__ . '/config.php';
$page_title = 'INDEX';
$active = 'home';
require __DIR__ . '/partials/header.php';
?>
<section class="hero">
  <div class="kicker">[ local security lab ]</div>
  <h1>SEC//LAB &mdash; 脆弱性実験室</h1>
  <p>Laboratorium lokal berisi dua kerentanan sengaja: <strong>SQL Injection</strong> dan
  <strong>Command Injection</strong>. Setiap modul punya versi <strong>VULNERABLE</strong>
  dan versi <strong>PATCHED</strong> supaya bisa dibandingkan langsung.</p>
</section>

<div class="grid cards">

  <div class="card">
    <span class="float-kanji">脆弱</span>
    <h2>// 01 &nbsp; sql injection</h2>
    <h3>Query tanpa prepared statement</h3>
    <p>Input login disisipkan langsung ke string SQL, sehingga bisa di-bypass
    dengan <code>' OR '1'='1' -- </code>.</p>
    <div class="actions">
      <a class="btn" href="<?= APP_BASE ?>/vulnerable/login.php">VULNERABLE ::&gt;</a>
      <a class="btn ghost" href="<?= APP_BASE ?>/secure/login.php">PATCHED</a>
    </div>
  </div>

  <div class="card">
    <span class="float-kanji">侵入</span>
    <h2>// 02 &nbsp; command injection</h2>
    <h3>shell_exec tanpa sanitasi</h3>
    <p>Host ping dijalankan lewat shell apa adanya, bisa dirantai dengan
    <code>127.0.0.1 &amp; whoami</code>.</p>
    <div class="actions">
      <a class="btn" href="<?= APP_BASE ?>/vulnerable/ping.php">VULNERABLE ::&gt;</a>
      <a class="btn ghost" href="<?= APP_BASE ?>/secure/ping.php">PATCHED</a>
    </div>
  </div>

  <div class="card">
    <span class="float-kanji">設定</span>
    <h2>// setup</h2>
    <h3>Kalau belum jalan</h3>
    <p>Import <code>db.sql</code> lewat phpMyAdmin. Pastikan XAMPP
    (Apache + MySQL) hidup dan folder ada di <code>htdocs</code>.</p>
    <div class="actions">
      <a class="btn ghost" href="http://localhost/phpmyadmin/" target="_blank">phpMyAdmin ::&gt;</a>
    </div>
  </div>

</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
