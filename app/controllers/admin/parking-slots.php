<?php

function ParkingSlotsController()
{
    $currentPage = $_GET['page'] ?? '1';

    $response = ParkingModel::getInstance()->searchParkingSlotsAdmin(1, 10);

    return $response;
};
