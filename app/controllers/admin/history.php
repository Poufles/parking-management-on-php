<?php

function HistoryController()
{
    $currentPage  = $_GET['page'] ?? 1;
    $limit        = 10;
    $search       = $_GET['search'] ?? null;
    $filterDate   = $_GET['filter_date'] ?? null;
    $filterType   = $_GET['filter_type'] ?? null;
    $filterAcct   = $_GET['filter_acct'] ?? null;
    $dateFrom     = $_GET['date_from'] ?? null;
    $dateTo       = $_GET['date_to'] ?? null;

    $response = HistoryModel::getInstance()->getHistory(
        $currentPage, 
        $limit, 
        $search, 
        $filterDate, 
        $filterType, 
        $filterAcct, 
        $dateFrom, 
        $dateTo
    );

    return $response;
}