<?php

function ClientDashboardController()
{
    if (!isset($_SESSION['uid'])) {
        header('location: ' . APP_URL . "auth/login");
        exit;
    }

    $response = null;
    return $response;
}
