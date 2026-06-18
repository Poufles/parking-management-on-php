<?php

function VehicleAddController()
{
    $response = null;

    if (isset($_POST['add'])) {
        // ADD VALIDATIONS LATER
        // ADD VALIDATIONS LATER

        $uid = '1'; // CHANGE LATER
        $plate_number = $_POST['plate-number'];
        $vehicle_type = $_POST['vehicle-type'];
        $vehicle_document = $_FILES['vehicle-document'];
        $filename = $vehicle_document['name'];

        $response = VehicleModel::getInstance()->addNewVehicle($uid, $plate_number, $vehicle_type, $filename);
        echo $response['message'];

        return $response;
    }

    return $response;
};
