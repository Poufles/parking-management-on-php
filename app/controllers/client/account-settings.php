<?php

function AccountSettingsController() {
    if (isset($_POST['logout'])) {
        AccountModel::getInstance()->logoutAccount($_SESSION['uid']);

        setcookie('parcheggiamo-uid', '', 0, '/');
        setcookie('parcheggiamo-username', '', 0, '/');
        setcookie('parcheggiamo-account-type', '', 0, '/');

        header('location: ' . APP_URL . "auth/login");
    }
}