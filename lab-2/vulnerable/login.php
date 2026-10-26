<?php
require __DIR__ . '/../config.php';
$page_title = 'SQL Injection (VULNERABLE)';
$active = 'sqli-vuln';

$rows  = null;
$query = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $query = "SELECT id, username, role, email FROM users WHERE username = '$u' AND password = '$p'";
    try {
        $rows = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception $e) {
        $error = $e->getMessage();
    }
}
require __DIR__ . '/../partials/header.php';
?>
<section class="page-head">
  <a class="back" href="<?= APP_BASE ?>/index.php">&larr; HOME</a><br>
  <span class="tag vuln">VULNERABLE</span>
  <h1>SQL Injection <span class="kanji">脆弱性</span></h1>
  <p class="lead">Input disisipkan langsung ke string query &mdash; tanpa <em>prepared statement</em>.</p>
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
      <button class="btn vuln" type="submit">[ EXECUTE ]</button>
    </form>
    <div class="hint">
      payload:<br>
      <code>admin' -- </code><br>
      <code>' OR '1'='1' -- </code><br>
      <code>' UNION SELECT 1,username,password,'-' FROM users -- </code><br>
      <code>' UNION SELECT 1,label,value,'-' FROM secrets -- </code>
    </div>
  </div>

  <div class="card">
    <h2>// output</h2>
    <?php if ($query !== null): ?>
      <div class="terminal"><span class="p">mysql&gt;</span> <?= htmlspecialchars($query) ?></div>
    <?php endif; ?>

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
