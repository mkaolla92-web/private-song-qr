<?php
require __DIR__.'/../config.php'; require_admin();
$url=app_url('customer/access.php');
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ONE QR CODE</title><link rel="stylesheet" href="../assets/style.css"><script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script></head>
<body><main class="center"><div class="panel centertext"><h1>ONE QR CODE</h1><p>Use this same QR code for all customers and all songs.</p><div id="qrcode" class="qr"></div><p class="smalltext"><?=e($url)?></p><button class="button" onclick="downloadQR()">Download QR PNG</button><p><a href="index.php">Dashboard</a> · <a href="customer/access.php">Test customer page</a></p></div></main>
<script>new QRCode(document.getElementById('qrcode'),{text:<?=json_encode($url)?>,width:280,height:280,correctLevel:QRCode.CorrectLevel.H});function downloadQR(){let c=document.querySelector('#qrcode canvas');if(!c){alert('QR is still loading. Please wait one second.');return;}let a=document.createElement('a');a.download='private-song-all-songs-qr.png';a.href=c.toDataURL('image/png');a.click();}</script>
</body></html>
