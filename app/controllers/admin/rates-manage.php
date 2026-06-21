<?php

function RatesManageController()
{
    $response = null;

    if (isset($_POST['create'])) {
        $hours_id = $_POST['hours-id'];
        $vehicle_type_id = $_POST['vehicle-type-id'];
        $fee = $_POST['fee'] ?? null;

        if (empty($fee)) return [
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

    if ($_SERVER['REQUEST_METHOD'] && isset($_POST['edit'])) {
        echo 'Hello';
    }

    return $response;
};
