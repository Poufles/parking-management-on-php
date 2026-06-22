<?php

function HistoryController()
{
    
    $currentPage  = $_GET['page'] ?? 1;
    $limit        = 10;
    $search       = $_GET['search'] ?? '';
    $filterDate   = $_GET['filter_date'] ?? '';
    $filterType   = $_GET['filter_type'] ?? '';
    $filterAcct   = $_GET['filter_acct'] ?? '';
    $dateFrom     = $_GET['date_from'] ?? '';
    $dateTo       = $_GET['date_to'] ?? '';
    $clientUID    = $_SESSION['uid'] ?? null;

    $response = HistoryModel::getInstance()->getHistory(
        $currentPage,
        $limit,
        $search,
        $filterDate,
        $filterType,
        $filterAcct,
        $dateFrom,
        $dateTo,
        $clientUID
    );

    return $response;
}
