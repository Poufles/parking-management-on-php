<?php

function VehicleTypeCreateController()
{
    $response = null;

    if (isset($_POST['vehicle-type'])) {
        // ADD VALIDATOR LATER

        $new_vehicle_type = $_POST['vehicle-type'];
        
        $response = VehicleModel::getInstance()->createNewVehicleType($new_vehicle_type);
        echo($response['message']);

        return $response;
    }
    return $response;
}
