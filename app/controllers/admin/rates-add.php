<?php


function RatesAddController()
{
    $response = null;

    if (isset($_POST['add'])) {
        // ADD VALIDATORS LATER

        $hours_id = $_POST['hours'];
        $vehicle_type_id = $_POST['type'];
        $fee = $_POST['fee'];
        $isRateExist = RateModel::getInstance()->isRateExist($hours_id, $vehicle_type_id);

        if ($isRateExist['results']['isRateExist']) return [
            "status" => false,
            "message" => "This Rate Fee already exist!",
            "response" => []
        ];

        $response = RateModel::getInstance()->createRateFee($hours_id, $vehicle_type_id, $fee);

        return $response;
    }

    return $response;
}
