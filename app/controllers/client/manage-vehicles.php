<?php

function ManageVehiclesController()
{
    if (!isset($_SESSION['uid'])) {
        header('location: ' . APP_URL . "auth/login");
        exit;
    }

    $response = null;

    if (isset($_POST['add'])) {
        $plate_number = trim($_POST['plate-number']) ?? null;
        $vehicle_type_id = $_POST['vehicle-type-id'] ?? null;
        $vehicle_document = $_FILES['vehicle-document'] ?? null;

        $plateNumberValidation = Validation::getInstance()->isPlateNumberValid($plate_number);
        $response = Validation::getInstance()->areFieldsEmpty([
            'plate_number' => $plate_number,
            'vehicle_type_id' => $vehicle_type_id,
            'vehicle_document' => $vehicle_document,
        ]);

        if (!isset($response['results']['plate_number'])) $response['results']['plate_number'] = $plateNumberValidation;

        if (!$response['status']) return $response;
        
        $response = VehicleModel::getInstance()->addNewVehicle($_SESSION['uid'], $plate_number, $vehicle_type_id, $vehicle_document);
    }

    return $response;
}
