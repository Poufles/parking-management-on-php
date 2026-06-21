<?php

function ParkingSlotsManageRequestController()
{
    $response = null;

    if (isset($_POST['register'])) {
        $slot_id = $_POST['slot_id'];
        $uid = $_POST['uid'];
        $amount_to_pay = $_POST['amount_to_pay'];
        $payment = $_POST['payment'] ?? null;

        $response = Validation::getInstance()->areFieldsEmpty([
            'payment' => $payment
        ]);


        if (!$response['status']) return $response;

        $paymentValidation = Validation::getInstance()->isPaymentValid($amount_to_pay, $payment);

        if (!$paymentValidation['status']) {
            $response['results']['payment'] = $paymentValidation;

            return $response;
        }

        $response = ParkingModel::getInstance()->processPayment($slot_id, $payment, $amount_to_pay, $uid);

        if($response['status']) {
            header('location: ' . APP_URL . "admin/parking-slots");
            exit;
        }
    }

    return $response;
}
