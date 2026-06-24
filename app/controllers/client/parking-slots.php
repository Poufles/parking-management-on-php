<?php

function ParkingSlotsController() {
    $response = [];

    if (isset($_POST['time-out'])) {
        ParkingModel::getInstance()->requestTimeOut($_POST['slot_id']);

        unset($_POST['time-out']);
    }

    if (!isset($_GET['level'])) $_GET['level'] = 'L1';
    if (!isset($_GET['section'])) $_GET['section'] = 'A';
    if (!isset($_GET['place'])) $_GET['place'] = '1';

    $uid = $_SESSION['uid'];
    $level = $_GET['level'];
    $section = $_GET['section'];

    $response = ParkingModel::getInstance()->ParkingTableForClient($uid, $level, $section);

    return $response;
}