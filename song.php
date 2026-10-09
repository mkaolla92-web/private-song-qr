<?php
require __DIR__ . '/../config.php';
$session=$_SESSION['customer_access']??null;
if(!$session || empty($session['access_id']) || empty($session['expires']) || (int)$session['expires']<time()){unset($_SESSION['customer_access']);header('Location: access.php');exit;}
$st=$pdo->prepare("SELECT * FROM customer_access WHERE id=? AND is_active=1");$st->execute([(int)$session['access_id']]);$access=$st->fetch();
if(!$access || (!empty($access['expires_at']) && strtotime($access['expires_at'])<time())){unset($_SESSION['customer_access']);header('Location: access.php');exit;}
$slug=trim($_GET['s']??'');
$stmt=$pdo->prepare("SELECT * FROM songs WHERE slug=? AND is_active=1");$stmt->execute([$slug]);$song=$stmt->fetch();
if(!$song){http_response_code(404);exit('Song not found.');}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($song['title'])?></title><link rel="stylesheet" href="../assets/style.css"></head>
<body><main class="center"><div class="panel song"><?php if($song['cover_path']):?><img class="cover" src="../<?=e($song['cover_path'])?>" alt="Cover"><?php endif;?><h1><?=e($song['title'])?></h1><h3><?=e($song['artist'])?></h3><?php if($song['description']):?><p><?=nl2br(e($song['description']))?></p><?php endif;?>
<audio controls preload="metadata" src="media.php?s=<?=e($song['slug'])?>"></audio>
<a class="button full" href="download.php?s=<?=e($song['slug'])?>">⬇ Download</a>
<a class="text-link" href="songs.php">← Back to all songs</a> · <a class="text-link" href="logout.php">Lock / Logout</a>
</div></main></body></html>
