<?php
require_once __DIR__ . '/../config.php';
$page_title = $page_title ?? 'INDEX';
$active = $active ?? '';
$nav = [
    'home'       => ['HOME',      APP_BASE . '/index.php'],
    'sqli-vuln'  => ['SQLi ::>',  APP_BASE . '/vulnerable/login.php'],
    'sqli-sec'   => ['SQLi [FIX]',APP_BASE . '/secure/login.php'],
    'cmd-vuln'   => ['CMD ::>',   APP_BASE . '/vulnerable/ping.php'],
    'cmd-sec'    => ['CMD [FIX]', APP_BASE . '/secure/ping.php'],
];
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($page_title) ?> // SEC//LAB</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_BASE ?>/assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="brand">
    <span class="kanji">実験</span>
    <span class="logo">SEC<span class="accent">//</span>LAB</span>
  </div>
  <nav>
    <?php foreach ($nav as $key => $item): ?>
      <a href="<?= $item[1] ?>" class="<?= $active === $key ? 'on' : '' ?>"><?= $item[0] ?></a>
    <?php endforeach; ?>
  </nav>
</header>
<main class="wrap">
