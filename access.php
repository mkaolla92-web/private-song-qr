<?php
require __DIR__ . '/../config.php';

if(!empty($_SESSION['customer_access']['access_id'])){
    $st=$pdo->prepare("SELECT * FROM customer_access WHERE id=? AND is_active=1");
    $st->execute([(int)$_SESSION['customer_access']['access_id']]);
    $access=$st->fetch();
    if($access && empty($access['expires_at']) || ($access && strtotime($access['expires_at'])>=time())){
        if((int)$access['max_uses']===0 || (int)$access['uses'] <= (int)$access['max_uses']){
            header('Location: songs.php'); exit;
        }
    }
    unset($_SESSION['customer_access']);
}

$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $code=strtoupper(trim($_POST['password']??''));
    if($code==='')$error='Enter your password.';
    else{
        $st=$pdo->query("SELECT * FROM customer_access WHERE is_active=1 AND password_hash IS NOT NULL ORDER BY id DESC");
        $matched=null;
        foreach($st->fetchAll() as $access){
            if(!empty($access['expires_at']) && strtotime($access['expires_at'])<time())continue;
            if((int)$access['max_uses']>0 && (int)$access['uses']>=(int)$access['max_uses'])continue;
            if(password_verify($code,$access['password_hash'])){$matched=$access;break;}
        }
        if(!$matched){$error='Incorrect, expired, disabled, or already-used password.';}
        else{
            $pdo->prepare("UPDATE customer_access SET uses=uses+1,last_used_at=NOW() WHERE id=?")->execute([(int)$matched['id']]);
            session_regenerate_id(true);
            $_SESSION['customer_access']=['access_id'=>(int)$matched['id'],'customer_name'=>$matched['customer_name'],'expires'=>time()+1800];
            header('Location: songs.php');exit;
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Private Song Library</title><link rel="stylesheet" href="../assets/style.css"></head>
<body><main class="center"><div class="panel centertext"><h1>🔐 Private Song Library</h1><p>Enter the password given to you to open the complete song library.</p>
<?php if($error):?><div class="error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Customer password</label><input class="input" name="password" autocomplete="one-time-code" required><button class="button full">Open All Songs</button></form>
<p class="muted">After login, you can play or download any available song. Your session lasts 30 minutes.</p></div></main></body></html>
