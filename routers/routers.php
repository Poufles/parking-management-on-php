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
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/utils/Validation.php"
        ],
        'action' => 'LoginController',
        'view' => __DIR__ . "/../app/views/auth/login.php",
        'css' => APP_URL . "styles/auth/login.css",
        'page' => 'login',
        'title' => TITLE . "Log in"
    ],
    'auth/register/email' => [
        'controller' => __DIR__ . "/../app/controllers/auth/register.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/utils/Validation.php",
            __DIR__ . "/../app/services/MailService.php"
        ],
        'action' => 'RegisterController',
        'view' => __DIR__ . "/../app/views/auth/register.php",
        'css' => APP_URL . "styles/auth/register.css",
        'page' => 'register-email',
        'title' => TITLE . "Registration: Email"
    ],
    'auth/register/otp' => [
        'controller' => __DIR__ . "/../app/controllers/auth/register.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/utils/Validation.php",
        ],
        'action' => 'RegisterController',
        'view' => __DIR__ . "/../app/views/auth/register.php",
        'css' => APP_URL . "styles/auth/register.css",
        'page' => 'register-otp',
        'title' => TITLE . "Registration: OTP"
    ],
    'auth/register/details' => [
        'controller' => __DIR__ . "/../app/controllers/auth/register.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/utils/Validation.php"
        ],
        'action' => 'RegisterController',
        'view' => __DIR__ . "/../app/views/auth/register.php",
        'css' => APP_URL . "styles/auth/register.css",
        'page' => 'register-details',
        'title' => TITLE . "Registration: Details"
    ],
    'client/dashboard' => [
        'controller' => __DIR__ . "/../app/controllers/client/dashboard.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php"
        ],
        'action' => 'ClientDashboardController',
        'view' => __DIR__ . "/../app/views/client/dashboard.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'client-dashboard',
        'title' => TITLE . "Dashboard"
    ],
    'client/account/edit' => [
        'controller' => __DIR__ . "/../app/controllers/client/account-edit.php",
        'model' => [__DIR__ . "/../app/models/AccountModel.php"],
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
        'model' => [__DIR__ . "/../app/models/AccountModel.php"],
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
            __DIR__ . "/../app/models/FileModel.php",
            __DIR__ . "/../app/utils/Validation.php"
        ],
        'action' => 'ManageVehiclesController',
        'view' => __DIR__ . "/../app/views/client/manage-vehicles.php",
        'css' => APP_URL . "styles/client/manage-vehicles.css",
        // 'css' => APP_URL . "styles/index.css",
        'page' => 'manage-vehicles',
        'title' => TITLE . "Manage Vehicles"
    ],
    'client/vehicles/add' => [
        'controller' => __DIR__ . "/../app/controllers/client/vehicle-add.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/FileModel.php",
        ],
        'action' => 'VehicleAddController',
        'view' => __DIR__ . "/../app/views/client/vehicle-add.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'vehicle-add',
        'title' => TITLE . "Add Vehicle"
    ],
    'client/parking-slots' => [
        'controller' => __DIR__ . "/../app/controllers/client/parking-slots.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/ParkingModel.php"
        ],
        'action' => 'ParkingSlotsController',
        'view' => __DIR__ . "/../app/views/client/parking-slots.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'client-parking-slots',
        'title' => TITLE . "Parking Slots"
    ],
    'client/history' => [
        'controller' => __DIR__ . "/../app/controllers/client/history.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/ParkingModel.php"
        ],
        'action' => 'HistoryController',
        'view' => __DIR__ . "/../app/views/client/history.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'client-history',
        'title' => TITLE . "History"
    ],
    'client/account-settings' => [
        'controller' => __DIR__ . "/../app/controllers/client/account-settings.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/ParkingModel.php"
        ],
        'action' => 'AccountSettingsController',
        'view' => __DIR__ . "/../app/views/client/account-settings.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'client-settings',
        'title' => TITLE . "Account Settings"
    ],
    'admin/parking-slots' => [
        'controller' => __DIR__ . "/../app/controllers/admin/parking-slots.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/ParkingModel.php"
        ],
        'action' => 'ParkingSlotsController',
        'view' => __DIR__ . "/../app/views/admin/parking-slots.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'parking-slots',
        'title' => TITLE . "Parking Slots"
    ],
    'admin/vehicles/create' => [
        'controller' => __DIR__ . "/../app/controllers/admin/vehicle-type-create.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php"
        ],
        'action' => 'VehicleTypeCreateController',
        'view' => __DIR__ . "/../app/views/admin/vehicle-type-create.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'vehicle-type-create',
        'title' => TITLE . "Add Vehicle Type"
    ],
    'admin/rates' => [
        'controller' => __DIR__ . "/../app/controllers/admin/rates-manage.php",
        'model' => [
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/RateModel.php",
            __DIR__ . "/../app/models/HoursModel.php",
        ],
        'action' => 'RatesManageController',
        'view' => __DIR__ . "/../app/views/admin/rates-manage.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'rates-manage',
        'title' => TITLE . "Manage Rates"
    ],
    'admin/rates/add' => [
        'controller' => __DIR__ . "/../app/controllers/admin/rates-add.php",
        'model' => [
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/RateModel.php",
            __DIR__ . "/../app/models/HoursModel.php",
        ],
        'action' => 'RatesAddController',
        'view' => __DIR__ . "/../app/views/admin/rates-add.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'rates-add',
        'title' => TITLE . "Add Rate"
    ],
    'admin/rates/edit' => [
        'controller' => __DIR__ . "/../app/controllers/admin/rates-edit.php",
        'model' => [
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/RateModel.php",
            __DIR__ . "/../app/models/HoursModel.php",
        ],
        'action' => 'RatesEditController',
        'view' => __DIR__ . "/../app/views/admin/rates-add.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'rates-edit',
        'title' => TITLE . "Edit Rate"
    ],
    'admin/rates/delete' => [ // COULD BE REMOVED
        'controller' => __DIR__ . "/../app/controllers/admin/rates-delete.php",
        'model' => [
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/RateModel.php",
            __DIR__ . "/../app/models/HoursModel.php",
        ],
        'action' => 'RatesDeleteController',
        'view' => __DIR__ . "/../app/views/admin/rates-delete.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'rates-edit',
        'title' => TITLE . "Edit Rate"
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
