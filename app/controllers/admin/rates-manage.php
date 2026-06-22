<?php

function RatesManageController()
{
    $response = null;

    if (isset($_POST['create'])) {
        $hours_id = $_POST['hours-id'];
        $vehicle_type_id = $_POST['vehicle-type-id'];
        $fee = $_POST['fee'] ?? null;

        if (!isset($fee)) return [
            'status' => false,
            'message' => 'Please fill up this field !'
        ];

        if (filter_var($fee, FILTER_VALIDATE_INT) === false) return [
            'status' => false,
            'message' => 'Not a number !'
        ];

        $isRateExist = RateModel::getInstance()->isRateExist($hours_id, $vehicle_type_id);

        if ($isRateExist['results']['isRateExist']) return [
            'status' => false,
            'message' => 'Rate already exists !'
        ];

        $response = RateModel::getInstance()->createRateFee($hours_id, $vehicle_type_id, $fee);
        unset($_POST['create']);
        unset($_POST['hours-id']);
        unset($_POST['vehicle-type-id']);
        unset($_POST['fee']);
    }

    if (isset($_GET['rate_id'])) {
        $response = RateModel::getInstance()->getRateFeeInformation($_GET['rate_id']);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['edit'])) {
        $rate_id = $_GET['rate_id'] ?? null;
        $fee = $_POST['fee'] ?? NULL;

        echo 'Hello';

        if (!isset($fee)) return [
            'status' => false,
            'message' => 'Please fill up this field !'
        ];

        if (filter_var($fee, FILTER_VALIDATE_INT) === false) return [
            'status' => false,
            'message' => 'Not a number !'
        ];

        $response = RateModel::getInstance()->editRateFee($rate_id, $fee);

        if ($response['status']) {
            header('location: ' . APP_URL . 'admin/rates');
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
        $response = RateModel::getInstance()->isRateUsed($_POST['vehicle_type_id']);
        $isRateUsed = $response['response']['isRateUsed'];

        if ($isRateUsed) {
            $_POST['delete-error'] = true;
            return;
        }

        RateModel::getInstance()->deleteRateFee($_POST['rate-id']);
    }

    return $response;
};
