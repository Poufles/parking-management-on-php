<?php

function AccountSettingsLicenceController() {
    $uid = $_SESSION['uid'] ?? null;
    $licence = $_GET['view'] ?? null;

    $response = FileModel::getInstance()->getFile($uid, $licence, 'upload_id');

    if (!$response || empty($response['response']['filename'])) {
        http_response_code(404);
        echo "File does not exist !";
        exit;
    }

    $filename = $response['response']['filename'];

    $baseDir = __DIR__ . '/../../uploads/' . $uid . '/';
    $filepath = $baseDir . $filename;

    if (!file_exists($filepath)) {
        http_response_code(404);
        echo "Fichier introuvable.";
        exit;
    }

    if (strpos(realpath($filepath), realpath($baseDir)) !== 0) {
        http_response_code(403);
        echo "Accès interdit.";
        exit;
    }

    $pathInfo = pathinfo($filename);
    $extension = strtolower($pathInfo['extension'] ?? '');

    $contentType = match ($extension) {
        'jpg', 'jpeg' => 'image/jpeg',
        'png'         => 'image/png',
        'gif'         => 'image/gif',
        'pdf'         => 'application/pdf',
        'docx'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'doc'         => 'application/msword',
        'xlsx'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        default       => 'application/octet-stream',
    };

    header('Content-Type: ' . $contentType);
    header('Content-Disposition: inline; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: public, max-age=86400');
    header('Pragma: public');

    readfile($filepath);
    exit;
}