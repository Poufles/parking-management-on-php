<?php

function HistoryController() {
    $response = null;

    $currentPage = $_GET['page'] ?? '1';

    $response = HistoryModel::getInstance()->getHistoryByUid($_SESSION['uid'], $currentPage);

    return $response; 
}