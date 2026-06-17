<?php

function ParkingSlotsController()
{
    $currentPage = $_GET['page'] ?? '1';

    $response = ParkingModel::getInstance()->searchParkingSlots('', '', $currentPage);

    if ($currentPage > $response['results']['totalPages']) {
        header('location: ' . APP_URL . 'admin/parking-slots?page=1');
        exit;
    };

    return $response;
};
