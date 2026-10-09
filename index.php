<?php
require __DIR__ . '/config.php';
if(!empty($_SESSION['admin_id'])){header('Location: admin/index.php');exit;}
if(!empty($_SESSION['customer_access']['access_id'])){header('Location: customer/songs.php');exit;}
header('Location: customer/access.php'); exit;
