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

    public function searchVehicles($filterBy, $search, $page = 1, $limit = 10) {
        try {
        $connect = DB_CONNECT;

        $offset = ($page - 1) * $limit;
        $searchValue = $search . "%";

        $query = "
        SELECT
            v.vehicle_id,
            a.username,
            v.plate_number,
            vt.vehicle_type
        FROM tbl_vehicles v
        INNER JOIN tbl_accounts a
            ON v.uid = a.uid
        INNER JOIN tbl_vehicle_types vt
            ON v.vehicle_type_id = vt.vehicle_type_id
        ";

        if (empty($filterBy)) {
            $query .= "
            WHERE
                a.username LIKE ?
                OR v.plate_number LIKE ?
                OR vt.vehicle_type LIKE ?
            ";
        } else {
            switch ($filterBy) {
                case 'username':
                    $column = 'a.username';
                    break;

                case 'plate_number':
                    $column = 'v.plate_number';
                    break;

                case 'vehicle_type':
                    $column = 'vt.vehicle_type';
                    break;

                default:
                    throw new Exception("Invalid filter.");
            }

            $query .= " WHERE {$column} LIKE ? ";
        }

        $query .= " LIMIT ? OFFSET ? ";

        $stmt = $connect->prepare($query);

        if (empty($filterBy)) {
            $stmt->bind_param(
                "sssii",
                $searchValue,
                $searchValue,
                $searchValue,
                $limit,
                $offset
            );
        } else {
            $stmt->bind_param(
                "sii",
                $searchValue,
                $limit,
                $offset
            );
        }

        $success = $stmt->execute();

        if (!$success) {
            throw new Exception($stmt->error);
        }

        $result = $stmt->get_result();

        return [
            'status' => true,
            'message' => 'Vehicles retrieved successfully.',
            'results' => [
                'rows' => $result->fetch_all(MYSQLI_ASSOC)
            ]
        ];

    } catch (Exception $e) {
        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
    }
}