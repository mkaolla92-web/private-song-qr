<?php
require __DIR__.'/../config.php'; require_admin();
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $title=trim($_POST['title']??'');
    $artist=trim($_POST['artist']??'');
    $desc=trim($_POST['description']??'');
    if(!$title||!$artist)$error='Title and artist are required.';
    elseif(empty($_FILES['audio'])||$_FILES['audio']['error']!==UPLOAD_ERR_OK)$error='Choose an audio file.';
    elseif($_FILES['audio']['size']>MAX_AUDIO_BYTES)$error='Audio is larger than 50 MB.';
    else{
        $ext=strtolower(pathinfo($_FILES['audio']['name'],PATHINFO_EXTENSION));
        if(!in_array($ext,['mp3','wav','m4a','ogg'],true))$error='Use MP3, WAV, M4A or OGG.';
        else{
            $songDir=__DIR__.'/../uploads/songs';
            $coverDir=__DIR__.'/../uploads/covers';
            if(!is_dir($songDir))@mkdir($songDir,0755,true);
            if(!is_dir($coverDir))@mkdir($coverDir,0755,true);
            $slug=make_slug();
            $name=$slug.'.'.$ext;
            $path=$songDir.'/'.$name;
            if(!move_uploaded_file($_FILES['audio']['tmp_name'],$path))$error='Could not save audio. Check the uploads/songs folder permissions.';
            else{
                $cover=null;
                if(!empty($_FILES['cover']['name'])&&$_FILES['cover']['error']===UPLOAD_ERR_OK&&$_FILES['cover']['size']<=MAX_COVER_BYTES){
                    $ce=strtolower(pathinfo($_FILES['cover']['name'],PATHINFO_EXTENSION));
                    if(in_array($ce,['jpg','jpeg','png','webp'],true)){
                        $cn=$slug.'.'.$ce;
                        if(move_uploaded_file($_FILES['cover']['tmp_name'],$coverDir.'/'.$cn))$cover='uploads/covers/'.$cn;
                    }
                }
                $st=$pdo->prepare("INSERT INTO songs(title,artist,description,slug,audio_path,cover_path) VALUES(?,?,?,?,?,?)");
                $st->execute([$title,$artist,$desc,$slug,'uploads/songs/'.$name,$cover]);
                header('Location: qr.php'); exit;
            }
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>New Song</title><link rel="stylesheet" href="../assets/style.css"></head><body><main class="center"><div class="panel">
<h1>New Song</h1><?php if($error):?><div class="error"><?=e($error)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Title</label><input class="input" name="title" required><label>Artist</label><input class="input" name="artist" required>
<label>Description</label><textarea class="input" name="description" rows="4"></textarea>
<label>Audio</label><input class="input" type="file" name="audio" accept=".mp3,.wav,.m4a,.ogg" required>
<label>Cover</label><input class="input" type="file" name="cover" accept=".jpg,.jpeg,.png,.webp">
<button class="button">Save Song</button></form><p><a href="index.php">Back to dashboard</a></p></div></main></body></html>
