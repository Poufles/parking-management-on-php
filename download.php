<?php
declare(strict_types=1);

$dataDir = __DIR__ . '/data';
if (is_dir($dataDir . '/sessions')) {
    session_save_path($dataDir . '/sessions');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (($_SESSION['user']['role'] ?? '') !== 'client') {
    header('Location: login.php');
    exit;
}

$attachmentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$attachments = $_SESSION['account_attachments'] ?? [];

foreach ($_SESSION['vehicle_attachments'] ?? [] as $vehicleAttachments) {
    $attachments = array_merge($attachments, $vehicleAttachments);
}

$attachment = null;
foreach ($attachments as $item) {
    if ((int) $item['id'] === $attachmentId) {
        $attachment = $item;
        break;
    }
}

if (!$attachment) {
    http_response_code(404);
    exit('Attachment not found.');
}

$path = __DIR__ . '/uploads/' . basename((string) $attachment['stored_name']);
$filename = basename((string) $attachment['original_name']);
$disposition = isset($_GET['view']) ? 'inline' : 'attachment';

if (!is_file($path)) {
    header('Content-Type: text/plain');
    header('Content-Disposition: ' . $disposition . '; filename="' . str_replace('"', '', $filename) . '"');
    echo "File placeholder for {$filename}.";
    exit;
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($path) ?: 'application/octet-stream';
header('Content-Type: ' . $mime);
header('Content-Disposition: ' . $disposition . '; filename="' . str_replace('"', '', $filename) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);