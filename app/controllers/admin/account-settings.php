<?php

function AccountSettingsController()
{
    $uid = $_SESSION['uid'];

    if (isset($_POST['logout'])) {
        setcookie('parcheggiamo-uid', '', 0, '/');
        setcookie('parcheggiamo-username', '', 0, '/');
        setcookie('parcheggiamo-account-type', '', 0, '/');
        session_destroy();
        header('location: ' . APP_URL . 'auth/login');
        exit;
    }

    if (isset($_POST['edit_profile'])) {
        $name     = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $email    = $_POST['email'] ?? '';
        $gender   = $_POST['gender'] ?? '';
        $phone    = $_POST['phone'] ?? '';

        $response = AccountModel::getInstance()->editAccount($uid, $name, $username, $email, $gender, $phone);

        if ($response['status']) {
            setcookie('parcheggiamo-username', $username, time() + 9999, '/');
        }

        $stmt = DB_CONNECT->prepare("SELECT * FROM tbl_accounts WHERE uid = ?");
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $account = $stmt->get_result()->fetch_assoc();

        return ['status' => $response['status'], 'message' => $response['message'], 'results' => ['account' => $account, 'tab' => 'profile']];
    }

    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = DB_CONNECT->prepare("SELECT password FROM tbl_accounts WHERE uid = ?");
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        $stmt2 = DB_CONNECT->prepare("SELECT * FROM tbl_accounts WHERE uid = ?");
        $stmt2->bind_param('i', $uid);
        $stmt2->execute();
        $account = $stmt2->get_result()->fetch_assoc();

        if (sha1($current) !== $row['password']) {
            return ['status' => false, 'message' => 'Current password is incorrect.', 'results' => ['account' => $account, 'tab' => 'password']];
        }

        if ($new !== $confirm) {
            return ['status' => false, 'message' => 'New passwords do not match.', 'results' => ['account' => $account, 'tab' => 'password']];
        }

        $hashed = sha1($new);
        $response = AccountModel::getInstance()->editAccountPassword($uid, $hashed);

        return ['status' => $response['status'], 'message' => $response['message'], 'results' => ['account' => $account, 'tab' => 'password']];
    }

    $stmt = DB_CONNECT->prepare("SELECT * FROM tbl_accounts WHERE uid = ?");
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $account = $stmt->get_result()->fetch_assoc();

    return ['status' => true, 'message' => '', 'results' => ['account' => $account, 'tab' => 'profile']];
}