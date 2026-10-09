<?php
require __DIR__ . '/../config.php';
$session=$_SESSION['customer_access']??null;
if(!$session || empty($session['access_id']) || empty($session['expires']) || (int)$session['expires']<time()){unset($_SESSION['customer_access']);header('Location: access.php');exit;}
$st=$pdo->prepare("SELECT * FROM customer_access WHERE id=? AND is_active=1");$st->execute([(int)$session['access_id']]);$access=$st->fetch();
if(!$access || (!empty($access['expires_at']) && strtotime($access['expires_at'])<time())){unset($_SESSION['customer_access']);header('Location: access.php');exit;}
$songs=$pdo->query("SELECT id,title,artist,description,slug,cover_path FROM songs WHERE is_active=1 ORDER BY created_at DESC,id DESC")->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>All Songs</title><link rel="stylesheet" href="../assets/style.css"></head>
<body><main class="wrap"><div class="panel"><div class="library-head"><div><h1>🎵 All Songs</h1><p class="muted">Welcome, <?=e((string)($access['customer_name']??'Customer'))?>.</p></div><a class="button" href="logout.php">Lock / Logout</a></div>
<?php if(!$songs):?><p>No songs are available yet.</p><?php else:?>
<div class="song-grid">
<?php foreach($songs as $song):?><article class="song-card"><?php if($song['cover_path']):?><img class="cover thumb" src="../<?=e($song['cover_path'])?>" alt="Cover"><?php endif;?><h2><?=e($song['title'])?></h2><p><?=e($song['artist'])?></p><?php if($song['description']):?><p class="muted"><?=e($song['description'])?></p><?php endif;?><a class="button full" href="song.php?s=<?=e($song['slug'])?>">▶ Play / Download</a></article><?php endforeach;?>
</div><?php endif;?></div></main></body></html>
