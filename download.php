<?php
require __DIR__ . '/../config.php';
$s=$_SESSION['customer_access']??null;
if(!$s || empty($s['access_id']) || empty($s['expires']) || (int)$s['expires']<time()){http_response_code(403);exit('Access denied.');}
$st=$pdo->prepare("SELECT * FROM customer_access WHERE id=? AND is_active=1");$st->execute([(int)$s['access_id']]);$a=$st->fetch();
if(!$a || (!empty($a['expires_at']) && strtotime($a['expires_at'])<time())){http_response_code(403);exit('Access denied.');}
$slug=trim($_GET['s']??'');$st=$pdo->prepare("SELECT title,audio_path,slug FROM songs WHERE slug=? AND is_active=1");$st->execute([$slug]);$song=$st->fetch();if(!$song){http_response_code(404);exit('Song not found.');}
$relative=ltrim(str_replace(['\\','../','..\\'],['/','',''],$song['audio_path']),'/');$file=realpath(__DIR__.'/../'.$relative);$base=realpath(__DIR__.'/../uploads/songs');
if(!$file||!$base||strpos($file,$base.DIRECTORY_SEPARATOR)!==0||!is_file($file)){http_response_code(404);exit('Audio file not found.');}
$ext=strtolower(pathinfo($file,PATHINFO_EXTENSION));$types=['mp3'=>'audio/mpeg','wav'=>'audio/wav','m4a'=>'audio/mp4','ogg'=>'audio/ogg'];$type=$types[$ext]??'application/octet-stream';
$downloadName=preg_replace('/[^A-Za-z0-9._-]+/','_',($song['title']?:'song')).'.'.$ext;
header('Content-Type: '.$type);header('Content-Length: '.filesize($file));header('Content-Disposition: attachment; filename="'.$downloadName.'"');header('Cache-Control: private, no-store');readfile($file);
