<?php

function HistoryController() {
    $response = null;

    $currentPage = $_GET['page'] ?? '1';

    $response = HistoryModel::getInstance()->getHistory($currentPage, 10, $_SESSION['uid']);

    return $response; 
}