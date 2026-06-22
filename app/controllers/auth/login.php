<?php

function LoginController()
{
    if (isset($_SESSION['uid'])) {
        header('location: ' . APP_URL . $_SESSION['account-type'] . "/parking-slots");
        exit;
    }

    $response = null;

    if (isset($_POST['login'])) {
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        $response = Validation::getInstance()->areFieldsEmpty([
            'username' => $username,
            'password' => $password
        ]);

        if (!$response['status']) return $response;

        $response = AccountModel::getInstance()->isInSession($username);

        if ($response['status']) {
            $response['results']['username'] = $response;
            $response['results']['username']['status'] = false;
            
            return $response;
        }

        $response = AccountModel::getInstance()->loginAccount($username, $password);

        if ($response['status']) {
            $results = $response['results'];
            $uid = $results['uid'];
            $username = $results['username'];
            $account_type = $results['account_type'];

            setcookie('parcheggiamo-uid', $uid, time() + 9999, "/");
            setcookie('parcheggiamo-username', $username, time() + 9999, "/");
            setcookie('parcheggiamo-account-type', $account_type, time() + 9999, "/");

            header('location: ' . APP_URL . "$account_type/parking-slots");
            exit;
        }
    }

    return $response;
};
