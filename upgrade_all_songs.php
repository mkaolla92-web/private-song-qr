<?php
require __DIR__ . '/config.php'; require_admin();
$messages=[];
try {
    $check=$pdo->query("SHOW COLUMNS FROM customer_access LIKE 'song_id'")->fetch();
    if($check){
        $fk=$pdo->prepare("SELECT DISTINCT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE CONSTRAINT_SCHEMA=DATABASE() AND TABLE_SCHEMA=DATABASE() AND TABLE_NAME='customer_access' AND COLUMN_NAME='song_id' AND REFERENCED_TABLE_NAME='songs' AND REFERENCED_COLUMN_NAME='id'");
        $fk->execute();
        $constraints=$fk->fetchAll(PDO::FETCH_COLUMN);
        foreach($constraints as $constraint){ if($constraint && $constraint!=='PRIMARY'){ $pdo->exec('ALTER TABLE customer_access DROP FOREIGN KEY `'.str_replace('`','``',$constraint).'`'); $messages[]='Removed old song relationship: '.$constraint; } }
        $pdo->exec("ALTER TABLE customer_access DROP COLUMN song_id");
        $messages[]='Removed song_id from customer_access.';
    } else {
        $messages[]='song_id is already removed. No database migration was needed.';
    }
    $pdo->query("SELECT 1 FROM songs LIMIT 1");
    $messages[]='Database upgrade completed successfully.';
} catch(Throwable $e){
    http_response_code(500);
    $messages[]='Upgrade failed: '.$e->getMessage();
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Upgrade</title><link rel="stylesheet" href="assets/style.css"></head><body><main class="center"><div class="panel"><h1>All Songs Upgrade</h1><?php foreach($messages as $m):?><p><?=e($m)?></p><?php endforeach;?><div class="success"><b>Next:</b> open the admin dashboard and create customer passwords without selecting a song.</div><p><b>Important:</b> After confirming it worked, delete <code>upgrade_all_songs.php</code> from File Manager.</p><p><a class="button" href="admin/index.php">Open Dashboard</a></p></div></main></body></html>
