<?php

function AccountDeleteController() {
    if (isset($_POST['delete'])) {
        // ADD INPUT VALIDATION LATER

        echo $_POST['uid'];
        $response = AccountModel::getInstance()->deleteAccount($_POST['uid']);

        echo $response['message'];

        return $response;
    }

    return ;
};