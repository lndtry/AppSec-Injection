<?php
require __DIR__ . '/../config.php';
$page_title = 'Command Injection (PATCHED)';
$active = 'cmd-sec';

$cmd   = null;
$out   = null;
$error = null;

if (isset($_GET['host']) && $_GET['host'] !== '') {
    $host = $_GET['host'];
    if (!preg_match('/^[a-zA-Z0-9.\-]{1,255}$/', $host)) {
        $error = 'Input ditolak: hanya huruf, angka, titik, dan dash yang diizinkan.';
    } else {
        $cmd = 'ping -n 1 ' . escapeshellarg($host);
        $out = shell_exec($cmd);
    }
}
require __DIR__ . '/../partials/header.php';
?>
<section class="page-head">
  <a class="back" href="<?= APP_BASE ?>/index.php">&larr; HOME</a><br>
  <span class="tag sec">PATCHED</span>
  <h1>Command Injection <span class="kanji">対策済</span></h1>
  <p class="lead">Input divalidasi pakai <em>whitelist</em> regex lalu di-<code>escapeshellarg()</code>.
  Karakter meta shell tidak akan dieksekusi.</p>
</section>

<div class="grid two">
  <div class="card">
    <h2>// network tools</h2>
    <form method="get" autocomplete="off">
      <div class="field">
        <label>host / ip</label>
        <input type="text" name="host" value="<?= htmlspecialchars($_GET['host'] ?? '') ?>" placeholder="127.0.0.1">
      </div>
      <button class="btn" type="submit">[ PING ]</button>
    </form>
    <div class="hint">
      Coba payload yang sama:<br>
      <code>127.0.0.1 &amp; whoami</code><br>
      &rarr; <span class="muted">input ditolak</span>, tidak ada perintah yang jalan.
    </div>
  </div>

  <div class="card">
    <h2>// output</h2>
    <?php if ($error !== null): ?>
      <div class="result no">DITOLAK — <?= htmlspecialchars($error) ?></div>
    <?php elseif ($cmd !== null): ?>
      <div class="terminal"><span class="p">cmd&gt;</span> <?= htmlspecialchars($cmd) ?></div>
      <?php if ($out === null || $out === false || trim((string)$out) === ''): ?>
        <div class="result no">(no output)</div>
      <?php else: ?>
        <pre class="terminal"><?= htmlspecialchars((string)$out) ?></pre>
      <?php endif; ?>
    <?php else: ?>
      <p class="muted">Menunggu input…</p>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
