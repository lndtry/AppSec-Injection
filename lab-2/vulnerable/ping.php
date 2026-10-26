<?php
require __DIR__ . '/../config.php';
$page_title = 'Command Injection (VULNERABLE)';
$active = 'cmd-vuln';

$cmd = null;
$out = null;

if (isset($_GET['host']) && $_GET['host'] !== '') {
    $host = $_GET['host'];
    $cmd = 'ping -n 1 ' . $host;
    $out = shell_exec($cmd);
}
require __DIR__ . '/../partials/header.php';
?>
<section class="page-head">
  <a class="back" href="<?= APP_BASE ?>/index.php">&larr; HOME</a><br>
  <span class="tag vuln">VULNERABLE</span>
  <h1>Command Injection <span class="kanji">侵入</span></h1>
  <p class="lead">Nilai <code>host</code> ditempel langsung ke perintah shell. Karakter
  <code>&amp;</code> bisa merantai perintah tambahan.</p>
</section>

<div class="grid two">
  <div class="card">
    <h2>// network tools</h2>
    <form method="get" autocomplete="off">
      <div class="field">
        <label>host / ip</label>
        <input type="text" name="host" value="<?= htmlspecialchars($_GET['host'] ?? '') ?>" placeholder="127.0.0.1">
      </div>
      <button class="btn vuln" type="submit">[ PING ]</button>
    </form>
    <div class="hint">
      payload (windows):<br>
      <code>127.0.0.1 &amp; whoami</code><br>
      <code>127.0.0.1 &amp; dir</code><br>
      <code>127.0.0.1 &amp; ipconfig</code>
    </div>
  </div>

  <div class="card">
    <h2>// output</h2>
    <?php if ($cmd !== null): ?>
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
