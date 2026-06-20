<?php

function ManageVehiclesController()
{
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

    if (isset($_GET['delete_vehicle_id'])) {
        $vehicle_id = $_GET['delete_vehicle_id'];

        $response = ParkingModel::getInstance()->searchVehicle($vehicle_id);
        
        if ($response['response']['count'] == 0) {
            VehicleModel::getInstance()->deleteVehicle($vehicle_id);
            header('location: ' . APP_URL . 'client/vehicles');
        }
    }

    return $response;
}
