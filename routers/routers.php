<?php

// MAKE SURE TO CHANGE THIS PART
DEFINE('APP_URL', '/projets/parking-management-on-php/public/');
DEFINE('TITLE', 'Parcheggiamo - ');

return [
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
    'client/vehicles' => [
        'controller' => __DIR__ . "/../app/controllers/client/manage-vehicles.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/RateModel.php",
            __DIR__ . "/../app/models/FileModel.php",
            __DIR__ . "/../app/utils/Validation.php",
            __DIR__ . "/../app/models/ParkingModel.php"
        ],
        'action' => 'ManageVehiclesController',
        'view' => __DIR__ . "/../app/views/client/manage-vehicles.php",
        'css' => APP_URL . "styles/client/manage-vehicles.css",
        // 'css' => APP_URL . "styles/index.css",
        'page' => 'manage-vehicles',
        'title' => TITLE . "Manage Vehicles"
    ],
    'client/vehicles/view' => [
        'controller' => __DIR__ . "/../app/controllers/client/vehicle-view.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/FileModel.php",
        ],
        'action' => 'VehicleViewController',
        'view' => __DIR__ . "/../app/views/client/vehicle-view.php",
        'css' => APP_URL . "styles/index.css",
        'page' => 'vehicle-view',
        'title' => TITLE . "View Vehicle"
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
        'css' => APP_URL . "styles/client/parking-slots.css",
        'page' => 'client-parking-slots',
        'title' => TITLE . "Parking Slots"
    ],
    'client/parking-slots/park-in' => [
        'controller' => __DIR__ . "/../app/controllers/client/parking-slots-park-in.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/ParkingModel.php"
        ],
        'action' => 'ParkInController',
        'view' => __DIR__ . "/../app/views/client/parking-slots-park-in.php",
        'css' => APP_URL . "styles/client/parking-slots-park-in.css",
        'page' => 'client-park-in',
        'title' => TITLE . "Park In"
    ],
    'client/history' => [
        'controller' => __DIR__ . "/../app/controllers/client/history.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/ParkingModel.php",
            __DIR__ . "/../app/models/HistoryModel.php"
        ],
        'action' => 'HistoryController',
        'view' => __DIR__ . "/../app/views/client/history.php",
        'css' => APP_URL . "styles/client/history.css",
        'page' => 'client-history',
        'title' => TITLE . "Parking History"
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
            __DIR__ . "/../app/models/ParkingModel.php",
            __DIR__ . "/../app/models/RateModel.php"
        ],
        'action' => 'ParkingSlotsController',
        'view' => __DIR__ . "/../app/views/admin/parking-slots.php",
        'css' => APP_URL . "styles/admin/parking-slots.css",
        'page' => 'parking-slots',
        'title' => TITLE . "Parking Slots"
    ],
    'admin/parking-slots/manage-request' => [
        'controller' => __DIR__ . "/../app/controllers/admin/parking-slots-manage-request.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/ParkingModel.php",
            __DIR__ . "/../app/models/RateModel.php",
            __DIR__ . "/../app/utils/Validation.php"
        ],
        'action' => 'ParkingSlotsManageRequestController',
        'view' => __DIR__ . "/../app/views/admin/parking-slots-manage-request.php",
        'css' => APP_URL . "styles/admin/parking-slots.css",
        'page' => 'parking-slots-manage-request',
        'title' => TITLE . "Manage Request"
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
            __DIR__ . "/../app/models/RateModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/ParkingModel.php",
            __DIR__ . "/../app/models/HoursModel.php",
        ],
        'action' => 'RatesManageController',
        'view' => __DIR__ . "/../app/views/admin/rates-manage.php",
        'css' => APP_URL . "styles/admin/rates.css",
        'page' => 'rates-manage',
        'title' => TITLE . "Manage Rates"
    ],
    'admin/vehicles' => [
        'controller' => __DIR__ . "/../app/controllers/admin/vehicles-manage.php",
        'model' => [
            __DIR__ . "/../app/models/RateModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/ParkingModel.php",
            __DIR__ . "/../app/models/HoursModel.php",
            __DIR__ . "/../app/models/AccountModel.php"
        ],
        'action' => 'VehiclesManageController',
        'view' => __DIR__ . "/../app/views/admin/vehicles-manage.php",
        'css' => APP_URL . "styles/admin/rates.css",
        'page' => 'vehicles-manage',
        'title' => TITLE . "Manage Vehicles"
    ],
    'admin/history' => [
        'controller' => __DIR__ . "/../app/controllers/admin/history.php",
        'model' => [
            __DIR__ . "/../app/models/HistoryModel.php",
            __DIR__ . "/../app/models/AccountModel.php",
            __DIR__ . "/../app/models/VehicleModel.php",
            __DIR__ . "/../app/models/RateModel.php",
        ],
        'action' => 'HistoryController',
        'view' => __DIR__ . "/../app/views/admin/history.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/config.css",
        'page' => 'admin-history',
        'title' => TITLE . "History"
    ],
    'admin/account-settings' => [
        'controller' => __DIR__ . "/../app/controllers/admin/account-settings.php",
        'model' => [
            __DIR__ . "/../app/models/AccountModel.php",
        ],
        'action' => 'AccountSettingsController',
        'view' => __DIR__ . "/../app/views/admin/account-settings.php",
        // 'css' => "../public/styles/client/config.css",
        'css' => APP_URL . "styles/index.css",
        'page' => 'account-settings',
        'title' => TITLE . "Account Settings"
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
