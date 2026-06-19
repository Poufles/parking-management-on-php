<?php

require __DIR__ . "/../app/utils/database.php";
require __DIR__ . "/../app/utils/session.php";

$routes = require  __DIR__ . "/../routers/routers.php";

$url = $_GET['url'] ?? "";

$userTypes = ['client, admin'];
$userInApp = in_array(explode('/', $url)[0], $userTypes);

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
$page = $route['page'];
$css = $route['css'];

$_SESSION['page'] = $route['page'];

$response = $action();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $css ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title><?= $title ?></title>
</head>

<body>
    <!-- <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= APP_URL ?>">{} dev tool</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= APP_URL ?>">Homepage</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Accounts
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL . "auth/login" ?>">Login Account</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL . "auth/register/email" ?>">Create Account</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL . "client/account/edit" ?>">Edit Account</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL . "client/account/delete" ?>">Delete Account</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Vehicles
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL . "client/vehicles/add" ?>">Add Vehicles (Client)</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL . "client/vehicles/" ?>">Vehicles (Admin)</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL . "admin/vehicles/create" ?>">Create Vehicle Type (Admin)</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Parking Slots
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL . "admin/parking-slots" ?>">Parking Slots (Admin View)</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Rates
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL . "admin/rates" ?>">Show Rates</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL . "admin/rates/add" ?>">Add Rates</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav> -->
    <main id="<?= $page ?>">
        <?php
        require_once $route['view'];
        ?>
    </main>
</body>

</html>