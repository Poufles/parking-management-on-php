<?php

function VehicleTypeCreateController()
{
    $response = null;

    if (isset($_POST['vehicle-type'])) {

        $new_vehicle_type = trim($_POST['vehicle-type'] ?? '');

        $response = Validation::getInstance()->areFieldsEmpty([
            'vehicle type' => $new_vehicle_type
        ]);

        if (!$response['status']) {
            header('location: ' . APP_URL . 'admin/vehicles?error=' . urlencode('Vehicle type name cannot be empty.'));
            exit;
        }

        if (strlen($new_vehicle_type) < 2 || strlen($new_vehicle_type) > 30) {
            header('location: ' . APP_URL . 'admin/vehicles?error=' . urlencode('Vehicle type name must be between 2 and 30 characters.'));
            exit;
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', $new_vehicle_type)) {
            header('location: ' . APP_URL . 'admin/vehicles?error=' . urlencode('Letters only, no numbers or special characters.'));
            exit;
        }

        $existing = VehicleModel::getInstance()->getAllVehicleTypes();
        $existingNames = array_map(fn($r) => strtolower($r['VEHICLE_TYPE']), $existing['results']['rows'] ?? []);

        if (in_array(strtolower($new_vehicle_type), $existingNames)) {
            header('location: ' . APP_URL . 'admin/vehicles?error=' . urlencode("'$new_vehicle_type' already exists."));
            exit;
        }

        $response = VehicleModel::getInstance()->createNewVehicleType($new_vehicle_type);

        if ($response['status']) {
            header('location: ' . APP_URL . 'admin/vehicles?success=' . urlencode("'$new_vehicle_type' added successfully!"));
        } else {
            header('location: ' . APP_URL . 'admin/vehicles?error=' . urlencode('Something went wrong. Please try again.'));
        }
        exit;
    }

    header('location: ' . APP_URL . 'admin/vehicles');
    exit;
}