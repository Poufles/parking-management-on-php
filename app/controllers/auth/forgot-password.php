<?php

function ForgotPasswordController()
{
    if (isset($_POST['send']) && !isset($_SESSION['otp'])) {
        $email = $_POST['email'] ?? null;

        $response = Validation::getInstance()->areFieldsEmpty([
            'email' => $email
        ]);

        $isEmailValid = Validation::getInstance()->isEmailValid($email);

        if (!$isEmailValid['status'] && $response['status']) {
            $response['results']['email'] = $isEmailValid;
            $response['status'] = false;
        }

        $isEmailExist = AccountModel::getInstance()->isEmailExist($email);

        if (!$isEmailExist['status'] && $response['status']) {
            $response['results']['email'] = $isEmailExist;
            $response['status'] = false;
        }
        if (!$response['status']) return $response;

        $otp_code = sprintf(
            '%03d-%03d-%03d',
            random_int(0, 999),
            random_int(0, 999),
            random_int(0, 999)
        );

        $_SESSION['otp'] = $otp_code;
        $body = "
        <h2>Forgot Password Code</h2>

        <p>Hello,</p>

        <p>Your One-Time Password (OTP) is:</p>

        <h1>$otp_code</h1>

        <p>Please use this code to reset your password.</p>

        <p>If you did not request this code, please ignore this email.</p>

        <p>Thank you.</p>
        ";

        $altBody = "
        Hello,

        Your One-Time Password (OTP) is:

        <h1>$otp_code</h1>

        Please use this code to reset your password.

        If you did not request this code, please ignore this email.

        Thank you.
        ";

        $emailSent = MailService::getInstance()->sendEmail(
            $email,
            null,
            'Parcheggiamo: Forgot Password',
            $body,
            $altBody
        );

        if ($emailSent['status']) {
            $uid = $isEmailExist['results']['uid'];
            $username = $isEmailExist['results']['username'];
            $account_type = $isEmailExist['results']['account_type'];

            setcookie('parcheggiamo-temp-uid', $uid, time() + 3000, '/');
            setcookie('parcheggiamo-temp-username', $username, time() + 3000, '/');
            setcookie('parcheggiamo-temp-account-type', $account_type, time() + 3000, '/');

            header('location: ' . APP_URL . "auth/forgot-password");
            exit;
        } else {
            $response['results']['error'] = [
                "status" => false,
                "message" => 'Something went wrong.',
            ];
            return $response;
        }
    }

    if (isset($_POST['confirm'])) {
        $otp = $_POST['otp'] ?? null;

        $response = Validation::getInstance()->areFieldsEmpty([
            'otp' => $otp
        ]);

        $isOTPValid = $_SESSION['otp'] == $otp;

        if (!$isOTPValid && $response['status']) {
            $response['results']['otp'] = [
                'status' => false,
                'message' => 'Wrong OTP Code !'
            ];
            $response['status'] = false;
        }

        if (!$response['status']) return $response;

        $_SESSION['reset-password'] = true;
        unset($_SESSION['otp']);
    }

    if (isset($_POST['reset-password'])) {
        $password = $_POST['password'] ?? null;

        $response = Validation::getInstance()->areFieldsEmpty([
            'password' => $password
        ]);

        $isPasswordValid = Validation::getInstance()->isPasswordValid($password);

        if (!$isPasswordValid['status'] && $response['status']) {
            $response['results']['password'] = $isPasswordValid;
            $response['status'] = false;
        }

        if (!$response['status']) return $response;

        $uid = $_COOKIE['parcheggiamo-temp-uid'];

        AccountModel::getInstance()->editAccountPassword($uid, $password);

        $username = $_COOKIE['parcheggiamo-temp-username'];

        $response = AccountModel::getInstance()->loginAccount($username, $password);

        if ($response['status']) {
            $results = $response['results'];
            $uid = $results['uid'];
            $username = $results['username'];
            $account_type = $results['account_type'];

            unset($_SESSION['reset-password']);
            setcookie('parcheggiamo-temp-uid', '', 0, '/');
            setcookie('parcheggiamo-temp-username', '', 0, '/');
            setcookie('parcheggiamo-temp-account-type', '', 0, '/');

            setcookie('parcheggiamo-uid', $uid, time() + 9999, "/");
            setcookie('parcheggiamo-username', $username, time() + 9999, "/");
            setcookie('parcheggiamo-account-type', $account_type, time() + 9999, "/");

            header('location: ' . APP_URL . "$account_type/parking-slots");
            exit;
        }
    }
}
