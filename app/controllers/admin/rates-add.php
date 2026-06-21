<?php

function RatesAddController()
{
    $response = null;

    if (isset($_POST['add'])) {

        $hours_id        = $_POST['hours'] ?? null;
        $vehicle_type_id = $_POST['type'] ?? null;
        $fee             = $_POST['fee'] ?? null;

        $response = Validation::getInstance()->areFieldsEmpty([
            'hours'        => $hours_id,
            'vehicle type' => $vehicle_type_id,
            'fee'          => $fee
        ]);

        if (!$response['status']) return $response;

        if (!is_numeric($fee) || $fee <= 0) {
            return [
                'status'  => false,
                'message' => 'Fee must be a valid positive number.',
                'results' => [
                    'fee' => [
                        'status'  => false,
                        'message' => 'Fee must be a valid positive number.'
                    ]
                ]
            ];
        }

        $isRateExist = RateModel::getInstance()->isRateExist($hours_id, $vehicle_type_id);

        if ($isRateExist['results']['isRateExist']) {
            return [
                'status'  => false,
                'message' => 'This Rate Fee already exists!',
                'results' => []
            ];
        }

        $response = RateModel::getInstance()->createRateFee($hours_id, $vehicle_type_id, $fee);

        return $response;
    }

    return $response;
}
