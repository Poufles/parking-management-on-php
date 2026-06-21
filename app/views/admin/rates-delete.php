<?php

function RatesDeleteController()
{
    $response = RateModel::getInstance()->deleteRateFee($_GET['rate_id']);

    if ($response['status']) {
        header("location: " . APP_URL . "admin/rates");
        exit;
    }

    header("location: " . APP_URL . "admin/rates?error=" . urlencode($response['message']));
    exit;
}
