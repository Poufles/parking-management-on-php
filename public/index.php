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
if (isset($route['model'])) require_once $route['model'];

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
    
    <link rel="stylesheet" href="<?= $css ?>">
    <title><?= $title ?></title>
</head>

<body>
    <nav>
        <a href="<?= APP_URL ?>">Index</a>
        <a href="<?= APP_URL . "auth/register" ?>">Create Account</a>
        <a href="<?= APP_URL . "client/account/edit" ?>">Edit Account</a>
        <a href="<?= APP_URL . "client/account/delete" ?>">Delete Account</a>
    </nav>
    <main>
        <?php
        require_once $route['view'];
        ?>
    </main>
</body>

</html>