<?php

require __DIR__ . "/../app/utils/database.php";
require __DIR__ . "/../app/utils/session.php";

$routes = require  __DIR__ . "/../routers/routers.php";

$url = $_GET['url'] ?? "";

$userTypes = ['client', 'admin'];
$userInApp = in_array(explode('/', $url)[0], $userTypes);

if ($userInApp && !isset($_SESSION['uid'])) {
    header('location: '. APP_URL . "auth/login");
}

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
    <link rel="stylesheet" href="<?= APP_URL . "styles/index.css" ?>">
    <link rel="stylesheet" href="<?= $css ?>">
    <link rel="stylesheet" href="<?= APP_URL . "styles/components/navigation/NavbarLateral.css" ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous" defer></script>
    <script src="<?= APP_URL . "scripts/components/navigation/NavbarLateral.js" ?>" defer></script>
    <title><?= $title ?></title>
</head>

<body class="<?php if($userInApp) echo "app" ?>">
    <?php
    if ($userInApp) require __DIR__ . "/../app/components/navigation/NavbarLateral.php";
    ?>
    <main id="<?= $page ?>">
        <?php
        require_once $route['view'];
        ?>
    </main>
</body>

</html>