<?php

function AccountEditController()
{
    if (isset($_POST['edit'])) {
        // ADD INPUT VALIDATION LATER

        $uid = '1';
        $name = $_POST['fullname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];

        echo $name . "<br>";
        echo $username . "<br>";
        echo $email . "<br>";
        echo $gender . "<br>";
        echo $phone . "<br>";

        $response = AccountModel::getInstance()->editAccount($uid, $name, $username, $email, $gender, $phone);
        echo $response['message'];

        return $response;
    };
};
