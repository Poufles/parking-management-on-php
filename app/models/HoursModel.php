<?php

class HoursModel
{
    private static ?HoursModel $instance = null;
    private mysqli $connect;
    public const TABLE = 'tbl_hours';

    private function __construct()
    {
        $this->connect = DB_CONNECT;
    }

    public static function getInstance(): HoursModel
    {
        if (self::$instance === null) {
            self::$instance = new HoursModel();
        }

        return self::$instance;
    }

    public function getHours() {
        try {
            $query = "
            SELECT hours_id, hours
            FROM ". self::TABLE ."
            ";

            $results = $this->connect->query($query);
            $rows = $results->fetch_all(MYSQLI_ASSOC);

            return [
                'status' => $results,
                'message' => $results 
                    ? 'Hours fetched successfully!'
                    : 'Hours fetched unsuccessfully!',
                'results' => [
                    'rows' => $rows,
                ]
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage(),
                'results' => []
            ];
        }
    }
}
