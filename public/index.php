<?php

require __DIR__ . "/../app/utils/session.php";
require __DIR__ . "/../database/DBConnect.php";

$routes = require  __DIR__ . "/../routers/routers.php";

$url = $_GET['url'] ?? "";
$route =
    !isset($routes[$url]) ?
    $routes['error404'] :
    $routes[$url];;

require_once $route['controller'];
if (isset($route['model'])) {
    foreach ($route['model'] as $model) {
        require_once $model;
    };
}

$action = $route['action'];
$title = $route['title'];
$css = $route['css'];

$_SESSION = $route['page'];

$response = $action();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= $css ?>">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title><?= $title ?></title>
</head>

<body>
    <nav>
        <span>Homepage</span>
        <a href="<?= APP_URL ?>">Index</a>
        <div class="block">
            <span>Accounts</span>
            <a href="<?= APP_URL . "auth/login" ?>">Login Account</a>
            <a href="<?= APP_URL . "auth/register" ?>">Create Account</a>
            <a href="<?= APP_URL . "client/account/edit" ?>">Edit Account</a>
            <a href="<?= APP_URL . "client/account/delete" ?>">Delete Account</a>
        </div>
        <div class="block">
            <span>Vehicles</span>
            <a href="<?= APP_URL . "client/vehicles" ?>">Show Client Vehicles</a>

        </div>
    </nav>
    <main>
        <?php
        require_once $route['view'];
        ?>
    </main>
</body>

</html>