<?php

function ParkInController()
{
    $response = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $slot_id = $_GET['slot_id'];
        $vehicle_id = $_POST['vehicle-id'];

        $response = ParkingModel::getInstance()->parkIn($slot_id, $vehicle_id);

        if ($response['status']) {
            header('location: ' . APP_URL . 'client/parking-slots');
            exit;
        }
    }

    return $response;
}
