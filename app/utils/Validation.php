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
            if (!isset($field)) {
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
        $isValid = preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email);

        return [
            'status' => $isValid,
            'message' => $isValid
                ? ''
                : 'Invalid Email Address !'
        ];
    }

    public function isPhoneValid($phone)
    {
        $isValid =  preg_match("/^\+?[0-9]{11,15}$/", $phone);
        
        return [
            'status' => $isValid,
            'message' => $isValid
                ? ''
                : 'Invalid Phone Number !'
        ];
    }

    public function isPasswordValid($password)
    {
        $isValid = preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/", $password);

        return [
            'status' => $isValid,
            'message' => $isValid
                ? ''
                : 'Invalid Password Format !'
        ];
    }

    public function isPasswordConfirmed($password, $conpass)
    {
        $isValid = $password == $conpass;

        return [
            'status' => $isValid,
            'message' => $isValid
                ? ''
                : 'Passwords do not match !'
        ];
    }

    public function isOTPCorrect($otp)
    {
        $isValid = $_SESSION['otp'] == $otp;

        return [
            'status' => $isValid,
            'message' => $isValid
                ? ''
                : 'Incorrect OTP Code !'
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
