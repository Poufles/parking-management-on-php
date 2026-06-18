<?php

function RatesEditController()
{
    // ADD VALIDATIONS LATER

    $rate_id = $_GET['rate_id'] ?? null;

    if (!isset($rate_id)) {
        header('location: ' . APP_URL . "admin/rates");
        exit;
    }

    $response = RateModel::getInstance()->searchRates('rate_id', $_GET['rate_id']);

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $fee = $_POST['fee'];

        echo 'Hello';
        $response = RateModel::getInstance()->editRateFee($rate_id, $fee);

        if ($response['status']) {
            header('location: ' . APP_URL . "admin/rates");
            exit;
        }

        return $response;
    }

    return $response;
}
