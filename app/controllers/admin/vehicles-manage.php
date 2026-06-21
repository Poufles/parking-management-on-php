<?php

function VehiclesManageController()
{
    $response = null;

    if (isset($_POST['create'])) {
        $vehicle = $_POST['vehicle'] ?? null;

        if (empty($vehicle)) return [
            'status' => false,
            'message' => 'Please fill up this field !'
        ];

        $isVehicleTypeExist = VehicleModel::getInstance()->isVehicleTypeExist($vehicle);


        if ($isVehicleTypeExist['results']['isExist']) return [
            'status' => false,
            'message' => 'Vehicle type already exists !'
        ];

        $response = VehicleModel::getInstance()->createNewVehicleType($vehicle);
        unset($_POST['vehicle']);
    }

    if ($_SERVER['REQUEST_METHOD'] && isset($_POST['delete'])) {
        $response = VehicleModel::getInstance()->isVehicleTypeUsed($_POST['vehicle_type_id']);
        $isVehicleTypeUsed = $response['results']['isUsed'];

        if ($isVehicleTypeUsed) {
            $_POST['delete-error'] = true;
            return;
        }

        $response = VehicleModel::getInstance()->deleteVehicleType($_POST['vehicle_type_id']);
    }

    return $response;
}
