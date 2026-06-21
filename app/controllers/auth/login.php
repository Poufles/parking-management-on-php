<?php

function LoginController()
{
    if (isset($_SESSION['uid'])) {
        header('location: ' . APP_URL . $_SESSION['account-type'] . "/dashboard");
        exit;
    }

    $response = null;

    if (isset($_POST['login'])) {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $response = Validation::getInstance()->areFieldsEmpty([
            'username' => $username,
            'password' => $password
        ]);

        if (!$response['status']) return $response;

        if (!preg_match('/^[a-zA-Z0-9._]+$/', $username)) {
            return [
                'status'  => false,
                'message' => 'Invalid username format.',
                'results' => [
                    'username' => [
                        'status'  => false,
                        'message' => 'Username can only contain letters, numbers, dots, and underscores.'
                    ]
                ]
            ];
        }

        if (strlen($username) > 50) {
            return [
                'status'  => false,
                'message' => 'Invalid username.',
                'results' => [
                    'username' => [
                        'status'  => false,
                        'message' => 'Username is too long.'
                    ]
                ]
            ];
        }

        if (strlen($password) < 6) {
            return [
                'status'  => false,
                'message' => 'Invalid password.',
                'results' => [
                    'password' => [
                        'status'  => false,
                        'message' => 'Password must be at least 6 characters.'
                    ]
                ]
            ];
        }

        $response = AccountModel::getInstance()->loginAccount($username, $password);

        if ($response['status']) {
            $results      = $response['results'];
            $uid          = $results['uid'];
            $username     = $results['username'];
            $account_type = $results['account_type'];

            setcookie('parcheggiamo-uid', $uid, time() + 9999, "/");
            setcookie('parcheggiamo-username', $username, time() + 9999, "/");
            setcookie('parcheggiamo-account-type', $account_type, time() + 9999, "/");

            header('location: ' . APP_URL . "$account_type/dashboard");
            exit;
        }
    }

    return $response;
}