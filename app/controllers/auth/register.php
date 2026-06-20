<?php

function RegisterController()
{
    /** @var string $page */

    if (isset($_SESSION['uid'])) {
        header('location: ' . APP_URL . $_SESSION['account-type'] . "/dashboard");
        exit;
    }

    if (isset($_SESSION['register']) && $_SESSION['page'] == 'register-otp') {
        header('location: ' . APP_URL . 'auth/register/details');
        exit;
    }

    $response = null;

    if (isset($_POST['otp-send'])) {
        $email = trim($_POST['email']) ?? null;

        $response = Validation::getInstance()->areFieldsEmpty([
            'email' => $email,
        ]);

        if (!$response['status']) return $response;

        $emailValidation = Validation::getInstance()->isEmailValid($email);

        if (!$emailValidation['status']) {
            $response['results']['email'] = $emailValidation;
            return $response;
        }

        $otp_code = sprintf(
            '%03d-%03d-%03d',
            random_int(0, 999),
            random_int(0, 999),
            random_int(0, 999)
        );

        $_SESSION['register-email'] = $email;
        $_SESSION['otp'] = $otp_code;
        $body = "
        <h2>Verification Code</h2>

        <p>Hello,</p>

        <p>Your One-Time Password (OTP) is:</p>

        <h1>$otp_code</h1>

        <p>Please use this code to complete your verification.</p>

        <p>If you did not request this code, please ignore this email.</p>

        <p>Thank you.</p>
        ";

        $altBody = "
        Hello,

        Your One-Time Password (OTP) is:

        <h1>$otp_code</h1>

        Please use this code to verify your email.

        If you did not request this code, please ignore this email.

        Thank you.
        ";

        $emailSent = MailService::getInstance()->sendEmail(
            $email,
            null,
            'Parcheggiamo: OTP Code',
            $body,
            $altBody
        );

        if ($emailSent['status']) {
            header('location: ' . APP_URL . "auth/register/otp");
            exit;
        } else {
            $response['results']['error'] = [
                "status" => false,
                "message" => 'Something went wrong.',
            ];
            return $response;
        }
    }

    if (isset($_POST['otp-confirm'])) {
        $otp = trim($_POST['otp']) ?? null;

        $response = Validation::getInstance()->areFieldsEmpty([
            'otp' => $otp,
        ]);

        if (!$response['status']) return $response;

        $otpValidation = Validation::getInstance()->isOTPCorrect($otp);

        if (!$otpValidation['status']) {
            $response['results']['otp'] = $otpValidation;
            return $response;
        }

        $_SESSION['register'] = true;
        header('location: ' . APP_URL . "auth/register/details");
        exit;
    }

    if (isset($_POST['register'])) {
        $name = trim($_POST['name']) ?? null;
        $username = trim($_POST['username']) ?? null;
        $phone = trim($_POST['phone']) ?? null;
        $gender = trim($_POST['gender']) ?? null;
        $password = trim($_POST['password']) ?? null;
        $conpass = trim($_POST['conpass']) ?? null;

        $phoneValidation = Validation::getInstance()->isPhoneValid($phone);

        $passwordConfirmedValidation = Validation::getInstance()->isPasswordConfirmed($password, $conpass);

        $usernameValidation = AccountModel::getInstance()->checkUsername($username);

        $response = Validation::getInstance()->areFieldsEmpty([
            'name' => $name,
            'username' => $username,
            'phone' => $phone,
            'gender' => $gender,
            'password' => $password,
            'conpass' => $conpass,
        ]);

        $response['results']['username'] = $usernameValidation;
        $response['results']['phone'] = $phoneValidation;

        $passwordValidation = Validation::getInstance()->isPasswordValid($password);

        $conpassValidation = $response['results']['conpass'] ?? null;
        
        if (!isset($conpassValidation)) {
            $response['results']['conpass'] = $passwordConfirmedValidation;
            $response['results']['password'] = $passwordValidation;
        }

        if (!$response['status'])  return $response;
        
        $response = AccountModel::getInstance()->createAccount($name, $username, $_SESSION['register-email'], $gender, $phone, $password, 'client');

        setcookie('parcheggiamo-uid', $response['results']['uid'], time() + 9999, '/');
        setcookie('parcheggiamo-username', $response['results']['username'], time() + 9999, '/');
        setcookie('parcheggiamo-account-type', $response['results']['account_type'], time() + 9999, '/');

        unset($_SESSION['register-email']);
        unset($_SESSION['register']);
        unset($_SESSION['otp']);

        header('location: ' . APP_URL . 'client/dashboard');
        exit;
    }

    return $response;
}
