<?php


function RegisterController()
{
    if (isset($_POST['submit'])) {
        // ADD INPUT VALIDATION LATER

        DEFINE("ACCOUNT_TYPE", 'client');

        $name = $_POST['fullname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];
        $password = $_POST['password'];
        $licence = $_POST['licence'];

        echo ACCOUNT_TYPE . "<br>";
        echo $name . "<br>";
        echo $username . "<br>";
        echo $email . "<br>";
        echo $gender . "<br>";
        echo $phone . "<br>";
        echo $password . "<br>";
        var_dump($licence);

        $response = AccountModel::getInstance()->createAccount($name, $username, $email, $gender, $phone, $password, ACCOUNT_TYPE, $licence);

        return $response;
    };
};
