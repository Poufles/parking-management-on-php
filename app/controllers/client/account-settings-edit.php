<?php

function AccountSettingsEditController()
{

    $response = null;

    if (isset($_POST['save'])) {
        $name = $_POST['name'] ?? null;
        $username = $_POST['username'] ?? null;
        $email = $_POST['email'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $licence = $_FILES['licence'] ?? null;
        $upload_id = $_POST['upload-id'] ?? null;
        $password = $_POST['password'] ?? null;

        $response = Validation::getInstance()->areFieldsEmpty([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'gender' => $gender,
            'phone' => $phone,
        ]);

        $emailValidator = Validation::getInstance()->isEmailValid($email);
        $phoneValidator = Validation::getInstance()->isPhoneValid($phone);

        if (!isset($response['results']['email']) && !$emailValidator['status']) {
            $response['results']['email'] = $emailValidator;
            $response['status'] = false;
        }

        if (!isset($response['results']['phone']) && !$phoneValidator['status']) {
            $response['results']['phone'] = $phoneValidator;
            $response['status'] = false;
        }


        if (!$response['status']) return $response;

        AccountModel::getInstance()->editAccount($_SESSION['uid'], $name, $username, $email, $gender, $phone);

        if (isset($licence)) {
            if (isset($upload_id)) FileModel::getInstance()->deleteFile($_SESSION['uid'], $upload_id);
            FileModel::getInstance()->uploadFile($_SESSION['uid'], 1, $licence);
        }

        if (!empty($password)) {

            $passwordValidator = Validation::getInstance()->isPasswordValid($password);

            if (!$passwordValidator['status']) {
                $response['results']['password'] = $passwordValidator;

                return $response;
            }

            AccountModel::getInstance()->editAccountPassword($_SESSION['uid'], $password);
        }

        header('location: ' . APP_URL . 'client/account-settings');
        exit;
    }
}
