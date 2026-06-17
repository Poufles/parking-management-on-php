<?php

// MAKE SURE TO CHANGE THIS PART
DEFINE('APP_URL', '/projets/parking-management-on-php/public/');
DEFINE('TITLE', 'Parcheggiamo - ');

return [
    '' => [
        'controller' => __DIR__ . "/../app/controllers/pages/homepage.php",
        'action' => 'HomepageController',
        'view' => __DIR__ . "/../app/views/pages/homepage.php",
        'css' => APP_URL . "styles/index.css",
        'page' => 'index',
        'title' => TITLE . "Homepage"
    ],
    'auth/login' => [
        'controller' => __DIR__ . "/../app/controllers/auth/login.php",
        'model' => [ __DIR__ . "/../app/models/AccountModel.php" ],
        'action' => 'LoginController',
        'view' => __DIR__ . "/../app/views/auth/login.php",
        // 'css' => "../public/styles/auth/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'login',
        'title' => TITLE . "Log in"
    ],
    'auth/register' => [
        'controller' => __DIR__ . "/../app/controllers/auth/register.php",
        'model' => [ __DIR__ . "/../app/models/AccountModel.php" ],
        'action' => 'RegisterController',
        'view' => __DIR__ . "/../app/views/auth/register.php",
        // 'css' => "../public/styles/auth/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'register',
        'title' => TITLE . "Registration"
    ],
    'client/account/edit' => [
        'controller' => __DIR__ . "/../app/controllers/client/account-edit.php",
        'model' => [ __DIR__ . "/../app/models/AccountModel.php" ],
        'action' => 'AccountEditController',
        'view' => __DIR__ . "/../app/views/client/account-edit.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'account-edit',
        'title' => TITLE . "Account Edit"
    ],
    // COULD BE DONE WITH EDIT vvv
    // COULD BE DONE WITH EDIT vvv
    // COULD BE DONE WITH EDIT vvv
    'client/account/delete' => [
        'controller' => __DIR__ . "/../app/controllers/client/account-delete.php",
        'model' => [ __DIR__ . "/../app/models/AccountModel.php" ],
        'action' => 'AccountDeleteController',
        'view' => __DIR__ . "/../app/views/client/account-delete.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'account-delete',
        'title' => TITLE . "Account Delete"
    ],
    'client/vehicles' => [
        'controller' => __DIR__ . "/../app/controllers/client/manage-vehicles.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
        ],
        'action' => 'ManageVehiclesController',
        'view' => __DIR__ . "/../app/views/client/manage-vehicles.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'manage-vehicles',
        'title' => TITLE . "Manage Vehicles"
    ],
    'error404' => [
        'controller' => __DIR__ . "/../app/controllers/pages/error-404.php",
        'action' => 'Error404Controller',
        'view' => __DIR__ . "/../app/views/pages/error-404.php",
        // 'css' => "../public/styles/pages/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'error404',
        'title' => TITLE . "Error 404"
    ]
];
