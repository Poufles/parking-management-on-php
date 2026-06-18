<?php

class VehicleModel
{
    private static ?VehicleModel $instance = null;
    private mysqli $connect;
    public const TABLE = 'tbl_vehicles';
    public const TABLE_VEHICLE_TYPES = 'tbl_vehicle_types';

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

    public function searchVehicles($filterBy, $search, $page = 1, $limit = 10)
    {
        try {
            $offset = ($page - 1) * $limit;

            $allowedFilters = [
                'username',
                'plate_number',
                'vehicle_type'
            ];

            $where = "";
            $params = [];
            $types = "";

            if (!empty($search)) {

                $searchInject = "%" . $search . "%";

                if (!empty($filterBy) && in_array($filterBy, $allowedFilters)) {

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
                    }

                    $where = "WHERE {$column} LIKE ?";
                    $params[] = $searchInject;
                    $types .= "s";
                } else {

                    $where = "
                WHERE a.username LIKE ?
                OR v.plate_number LIKE ?
                OR vt.vehicle_type LIKE ?
                ";

                    $params = [
                        $searchInject,
                        $searchInject,
                        $searchInject
                    ];

                    $types .= "sss";
                }
            }

            $query = "
            SELECT
                v.vehicle_id as vehicle_id,
                a.uid as uid,
                a.name as name,
                v.plate_number as plate_number,
                vt.vehicle_type as vehicle_type
            FROM " . self::TABLE . " v
            INNER JOIN " . AccountModel::getInstance()::TABLE . " a
                ON v.uid = a.uid
            INNER JOIN tbl_vehicle_types vt
                ON v.vehicle_type_id = vt.vehicle_type_id
            $where
            LIMIT ? OFFSET ?
            ";

            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param($types, ...$params);

            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Count query
            $countQuery = "
            SELECT COUNT(*) AS total
            FROM tbl_vehicles v
            INNER JOIN " . AccountModel::getInstance()::TABLE . " a
                ON v.uid = a.uid
            INNER JOIN tbl_vehicle_types vt
                ON v.vehicle_type_id = vt.vehicle_type_id
            $where
            ";

            $countStmt = $this->connect->prepare($countQuery);

            $countParams = array_slice($params, 0, -2);
            $countTypes = substr($types, 0, -2);

            if (!empty($countParams)) {
                $countStmt->bind_param($countTypes, ...$countParams);
            }

            $countStmt->execute();
            $total = $countStmt->get_result()->fetch_assoc()['total'];

            return [
                'status' => true,
                'message' => 'Vehicles fetched successfully',
                'results' => [
                    'rows' => $rows,
                    'page' => $page,
                    'limit' => $limit,
                    'totalPages' => ceil($total / $limit),
                    'totalItems' => $total
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

    public function getAllVehicleTypes()
    {
        try {
            $query = "
            SELECT *
            FROM " . self::TABLE_VEHICLE_TYPES . "
            ";

            $results = $this->connect->query($query);
            $rows = $results->fetch_all(MYSQLI_ASSOC);

            return [
                'status' => $results,
                'message' => 'Vehicles types fetched successfully',
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

    public function addNewVehicle($uid, $plate_number, $vehicle_type_id, $vehicle_document)
    {
        try {
            $query = "
            INSERT INTO " . self::TABLE . " (
                uid,
                plate_number,
                vehicle_type_id
            ) VALUES (?, ?, ?)
            ";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param(
                'isi',
                $uid,
                $plate_number,
                $vehicle_type_id
            );

            $stmt->execute();
            $db_vehicle_id = $stmt->insert_id;
            $results = FileModel::getInstance()->uploadFile($uid, 1, $vehicle_document, $db_vehicle_id);

            return [
                'status' => $results,
                'message' => $results
                    ? "Vehicle added successfully"
                    : "Something went wrong...",
                'results' => []
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