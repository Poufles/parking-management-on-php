<?php

class Validation
{
    private static ?Validation $instance = null;

    private function __construct() {}

    public static function getInstance(): Validation
    {
        if (self::$instance === null) {
            self::$instance = new Validation();
        }

        return self::$instance;
    }

    public function areFieldsEmpty($arrValues)
    {
        $isEmpty = count($arrValues);
        $results = [];

        foreach ($arrValues as $key => $field) {
            if (empty($field) || $field == 'default') {
                $isEmpty--;

                $results[$key] = [
                    'status' => false,
                    'message' => 'Please fill up this field.'
                ];
            }
        }

        return [
            "status" => $isEmpty == count($arrValues),
            "message" => $isEmpty == count($arrValues)
                ? ''
                : 'Missing fields.',
            "results" => $results
        ];
    }

    public function isEmailValid($email)
    {
        return [
            'status' => preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email),
            'message' => 'Invalid email address !'
        ];
    }

    public function isPhoneValid($phone)
    {
        return [
            'status' => preg_match("/^\+?[0-9]{10,15}$/", $phone),
            'message' => 'Invalid phone number !'
        ];
    }

    public function isPasswordValid($password)
    {
        return [
            'status' => preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/", $password),
            'message' => 'Invalid password format !'
        ];
    }

    public function isPasswordConfirmed($password, $conpass)
    {
        return [
            'status' => $password == $conpass,
            'message' => 'Passwords do not match !'
        ];
    }

    public function isOTPCorrect($otp)
    {
        return [
            'status' => $_SESSION['otp'] == $otp,
            'message' => 'Incorrect OTP Code !'
        ];
    }

    public function isPlateNumberValid($plate_number)
    {
        $isValid = preg_match('/^[A-Z]{3} \d{3,4}$/', $plate_number);

        return [
            'status' => $isValid,
            'message' => $isValid
                ? null
                : 'Invalid plate number !'
        ];
    }

    function isPaymentValid($amount_to_pay, $payment)
    {
        $isSufficient = $amount_to_pay <= $payment;

        return [
            'status' => $isSufficient,
            'message' => $isSufficient
                ? null
                : 'Insufficient money !'
        ];
    }
}
