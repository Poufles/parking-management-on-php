<?php
declare(strict_types=1);

const APP_NAME = 'Parcheggiamo';

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir . '/sessions')) {
    mkdir($dataDir . '/sessions', 0775, true);
}

session_save_path($dataDir . '/sessions');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function go(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = compact('type', 'message');
}

function take_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

function check_csrf(): void
{
    if (!hash_equals(csrf(), $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Invalid request token.');
    }
}

if (!empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'client') {
    go('client.php');
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $_SESSION['user'] = [
        'id' => 1,
        'name' => 'Client',
        'role' => 'client',
    ];
    go('client.php');
}

$flash = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Client Login | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="site.css?v=<?= filemtime(__DIR__ . '/site.css') ?>">
</head>
<body>
<main class="auth-page login-page">
    <section class="auth-card">
        <a class="brand" href="login.php">
            <img src="assets/logo.png" alt="">
            <span><?= APP_NAME ?></span>
        </a>
        <h1>Client Login</h1>

        <?php if ($flash) { ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php } ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= csrf() ?>">
            <button class="btn btn-primary" style="width:100%">Sign In</button>
        </form>
    </section>

    <section class="auth-visual">
        <img src="assets/park.jpg" alt="Modern parking garage">
    </section>
</main>
</body>
</html>
