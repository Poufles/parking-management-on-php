<?php

function ManageVehiclesController()
{
    $currentPage = $_GET['page'] ?? '1';
    
    $response = VehicleModel::getInstance()->searchVehicles('', '', $currentPage);

    if ($currentPage > $response['results']['totalPages']) {
        header('location: '. APP_URL . 'client/vehicles?page=1');
        exit;
    };

    return $response;
}
