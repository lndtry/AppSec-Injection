<?php
require __DIR__ . '/../config.php';
$page_title = 'SQL Injection (PATCHED)';
$active = 'sqli-sec';

$rows  = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $stmt = $conn->prepare('SELECT id, username, role, email FROM users WHERE username = ? AND password = ?');
    if ($stmt === false) {
        $error = $conn->error;
    } else {
        $stmt->bind_param('ss', $u, $p);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}
require __DIR__ . '/../partials/header.php';
?>
<section class="page-head">
  <a class="back" href="<?= APP_BASE ?>/index.php">&larr; HOME</a><br>
  <span class="tag sec">PATCHED</span>
  <h1>SQL Injection <span class="kanji">対策済</span></h1>
  <p class="lead">Pakai <em>prepared statement</em> + <code>bind_param()</code>. Input tidak pernah
  menyentuh string query, jadi payload diperlakukan sebagai data biasa.</p>
</section>

<div class="grid two">
  <div class="card">
    <h2>// login</h2>
    <form method="post" autocomplete="off">
      <div class="field">
        <label>username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="admin">
      </div>
      <div class="field">
        <label>password</label>
        <input type="text" name="password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>" placeholder="••••••">
      </div>
      <button class="btn" type="submit">[ EXECUTE ]</button>
    </form>
    <div class="hint">
      Coba payload yang sama:<br>
      <code>admin' -- </code><br>
      <code>' OR '1'='1' -- </code><br>
      <code>' UNION SELECT 1,label,value,'-' FROM secrets -- </code><br>
      &rarr; hasilnya <span class="muted">LOGIN FAILED</span>.
    </div>
  </div>

  <div class="card">
    <h2>// output</h2>
    <div class="terminal"><span class="p">pdo&gt;</span> SELECT id, username, role, email FROM users WHERE username = ? AND password = ?</div>

    <?php if ($error !== null): ?>
      <div class="result no">QUERY ERROR — <?= htmlspecialchars($error) ?></div>
    <?php elseif ($rows !== null): ?>
      <?php if (count($rows) > 0): ?>
        <div class="result ok">LOGIN SUCCESS — <?= count($rows) ?> row(s)</div>
        <table class="tbl">
          <tr><th>id</th><th>username</th><th>role</th><th>email</th></tr>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= htmlspecialchars((string)$r['id']) ?></td>
              <td><?= htmlspecialchars((string)$r['username']) ?></td>
              <td><?= htmlspecialchars((string)$r['role']) ?></td>
              <td><?= htmlspecialchars((string)$r['email']) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php else: ?>
        <div class="result no">LOGIN FAILED — 0 row</div>
      <?php endif; ?>
    <?php else: ?>
      <p class="muted">Menunggu input…</p>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
