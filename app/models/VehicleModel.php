<?php

class VehicleModel {
    private static ?VehicleModel $instance = null;
    private mysqli $connect;
    private const TABLE = 'tbl_accounts';

    private function __construct()
    {
        $this->connect = DB_CONNECT;
    }

    public static function getInstance(): VehicleModel
    {
        if (self::$instance === null) {
            self::$instance = new VehicleModel();
        }

        return self::$instance;
    }

}