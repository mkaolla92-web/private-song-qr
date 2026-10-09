<?php
require __DIR__ . '/../config.php';
function customer_session_ok(PDO $pdo): bool {
    $s=$_SESSION['customer_access']??null;
    if(!$s || empty($s['access_id']) || empty($s['expires']) || (int)$s['expires']<time()) return false;
    $st=$pdo->prepare("SELECT is_active,expires_at FROM customer_access WHERE id=?");$st->execute([(int)$s['access_id']]);$a=$st->fetch();
    return $a && (int)$a['is_active']===1 && (empty($a['expires_at']) || strtotime($a['expires_at'])>=time());
}
if(!customer_session_ok($pdo)){http_response_code(403);exit('Access denied.');}
$slug=trim($_GET['s']??'');$st=$pdo->prepare("SELECT audio_path,title FROM songs WHERE slug=? AND is_active=1");$st->execute([$slug]);$song=$st->fetch();
if(!$song){http_response_code(404);exit('Song not found.');}
$relative=ltrim(str_replace(['\\','../','..\\'],['/','',''],$song['audio_path']),'/');
$file=realpath(__DIR__.'/../'.$relative);$base=realpath(__DIR__.'/../uploads/songs');
if(!$file || !$base || strpos($file,$base.DIRECTORY_SEPARATOR)!==0 || !is_file($file)){http_response_code(404);exit('Audio file not found.');}
$size=filesize($file);$ext=strtolower(pathinfo($file,PATHINFO_EXTENSION));$types=['mp3'=>'audio/mpeg','wav'=>'audio/wav','m4a'=>'audio/mp4','ogg'=>'audio/ogg'];$type=$types[$ext]??'application/octet-stream';
header('Content-Type: '.$type);header('Accept-Ranges: bytes');header('Cache-Control: private, no-store');
$start=0;$end=$size-1;
if(isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/i',$_SERVER['HTTP_RANGE'],$m)){
    if($m[1]==='' && $m[2]===''){} else { $start=$m[1]===''?max(0,$size-(int)$m[2]):(int)$m[1]; $end=$m[2]===''?$end:(int)$m[2]; if($start>$end||$start<0||$end>=$size){header('Content-Range: bytes */'.$size);http_response_code(416);exit;} http_response_code(206);header('Content-Range: bytes '.$start.'-'.$end.'/'.$size);header('Content-Length: '.($end-$start+1)); }
}
if(http_response_code()!==206)header('Content-Length: '.$size);
$fp=fopen($file,'rb');fseek($fp,$start);$remaining=$end-$start+1;while($remaining>0 && !feof($fp)){ $chunk=fread($fp,min(8192,$remaining)); if($chunk===false)break; echo $chunk; $remaining-=strlen($chunk); flush(); }fclose($fp);
