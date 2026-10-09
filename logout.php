<?php
require __DIR__ . '/../config.php';
unset($_SESSION['customer_access']);
header('Location: access.php'); exit;
