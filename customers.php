<?php
require __DIR__.'/../config.php'; require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $id=(int)($_POST['id']??0);
    $action=$_POST['action']??'';
    if($id>0 && $action==='disable')$pdo->prepare("UPDATE customer_access SET is_active=0 WHERE id=?")->execute([$id]);
    if($id>0 && $action==='enable')$pdo->prepare("UPDATE customer_access SET is_active=1 WHERE id=?")->execute([$id]);
}
$rows=$pdo->query("SELECT * FROM customer_access ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Customers</title><link rel="stylesheet" href="../assets/style.css"></head><body><main class="wrap"><div class="panel"><h1>Customer Access</h1><div class="nav"><a class="button" href="customer_new.php">+ Create Password</a><a class="button" href="qr.php">ONE QR CODE</a><a href="index.php">Dashboard</a></div>
<table><tr><th>Customer</th><th>Uses</th><th>Expires</th><th>Status</th><th>Action</th></tr>
<?php foreach($rows as $r):?><tr><td><?=e($r['customer_name'])?></td><td><?=e((string)$r['uses'])?> / <?=((int)$r['max_uses']===0?'∞':e((string)$r['max_uses']))?></td><td><?=e($r['expires_at']??'Never')?></td><td><?=((int)$r['is_active']?'Active':'Disabled')?></td><td><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="action" value="<?=((int)$r['is_active']?'disable':'enable')?>"><button class="small"><?=((int)$r['is_active']?'Disable':'Enable')?></button></form></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="5" class="muted">No customers yet.</td></tr><?php endif;?>
</table></div></main></body></html>
