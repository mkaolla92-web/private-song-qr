<?php
require __DIR__.'/../config.php'; require_admin();
$error='';$plain='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $name=trim($_POST['customer_name']??'');
    $max=(int)($_POST['max_uses']??0);
    $expires=trim($_POST['expires_at']??'');
    if(!$name)$error='Customer name is required.';
    else{
        $plain=make_access_code();
        $hash=password_hash($plain,PASSWORD_DEFAULT);
        $st=$pdo->prepare("INSERT INTO customer_access(customer_name,password_hash,max_uses,expires_at) VALUES(?,?,?,?)");
        $st->execute([$name,$hash,max(0,$max),$expires!==''?str_replace('T',' ',$expires):null]);
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Customer Password</title><link rel="stylesheet" href="../assets/style.css"></head><body><main class="center"><div class="panel">
<h1>Create Customer Password</h1>
<?php if($plain):?><div class="success"><b>Give this password to <?=e($_POST['customer_name'])?>:</b><div class="code"><?=e($plain)?></div><p>Save it now. The system stores only the password hash.</p></div><?php endif;?>
<?php if($error):?><div class="error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Customer name</label><input class="input" name="customer_name" required>
<label>Maximum successful logins (0 = unlimited)</label><input class="input" type="number" name="max_uses" value="0" min="0">
<label>Expiration (optional)</label><input class="input" type="datetime-local" name="expires_at">
<button class="button">Generate Password</button></form>
<p><a href="customers.php">Customer Access</a> · <a href="index.php">Dashboard</a></p></div></main></body></html>
