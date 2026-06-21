<?php
 
function ManageAccountsController()
{
    $currentPage = $_GET['p'] ?? 1;
    $filterBy = $_GET['filter'] ?? '';
    $search = $_GET['search'] ?? '';
 
    // DELETE account
    if (isset($_GET['delete_uid'])) {
        $uid = $_GET['delete_uid'];
        AccountModel::getInstance()->deleteAccount($uid);
        header('location: ' . APP_URL . 'admin/manage-accounts');
        exit;
    }
 
    $response = AccountModel::getInstance()->searchAccounts($filterBy, $search, $currentPage, 10);
 
    return $response;
}