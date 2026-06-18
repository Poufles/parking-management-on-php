<?php

function RatesManageController()
{
    $currentPage = $_GET['page'] ?? '1';

    $response = RateModel::getInstance()->searchRates('', '', $currentPage);

    $totalPages = $response['results']['totalPages'];
    var_dump($response['message']); echo "<br>";

    if ($totalPages != 0 && $currentPage > $totalPages || $currentPage < 1) {
        // header('location: ' . APP_URL . 'admin/rates?page=1');
        exit;
    };

    return $response;
};
