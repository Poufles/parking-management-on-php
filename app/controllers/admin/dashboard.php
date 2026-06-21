<?php

function AdminDashboardController() {
    $response = ParkingModel::getInstance()->getDashboardStats();

    return $response;
}
