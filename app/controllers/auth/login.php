<?php

function LoginController()
{
    if (isset($_POST['login'])) {
        // ADD VALIDATORS LATER

        $username = $_POST['username'];
        $password = $_POST['password'];

        $response = AccountModel::getInstance()->loginAccount($username, $password);
        if ($response['status']) {
            echo $response['results']['uid'] . "<br>";
            echo $response['results']['username'] . "<br>";
            echo $response['results']['account_type'] . "<br>";
        }
    }
};
