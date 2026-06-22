<?php

function HistoryController()
{
    $response = null;

    $currentPage = $_GET['page'] ?? '1';

    $response = HistoryModel::getInstance()->getHistory($currentPage);

    $totalPages = $response['results']['total_pages'];

    if (($currentPage < 0 || $currentPage > $totalPages) && $totalPages != null) {
        header('location: ' . APP_URL . 'admin/history?page=1');
        exit;
    }

    return $response;
}
