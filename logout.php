<?php
declare(strict_types=1);

$dataDir = __DIR__ . '/data';
if (is_dir($dataDir . '/sessions')) {
    session_save_path($dataDir . '/sessions');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

session_destroy();
header('Location: login.php');
exit;
