<?php

class VehicleModel
{
    private static ?VehicleModel $instance = null;
    private mysqli $connect;
    private const VEHICLE_LIMIT = 3;
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
                'uid',
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
                        case 'uid':
                            $column = 'v.uid';
                            break;

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

    public function getAllVehicleTypes($withRates = true)
    {
        try {
            $query = "
            SELECT *
            FROM " . self::TABLE_VEHICLE_TYPES . "
            ";

            if (!$withRates) {
                $query = "
                SELECT 
                    v.vehicle_type_id as vehicle_type_id,
                    v.vehicle_type as vehicle_type
                FROM ". VehicleModel::getInstance()::TABLE_VEHICLE_TYPES ." v
                INNER JOIN ". RateModel::getInstance()::TABLE ." r ON v.vehicle_type_id = r.vehicle_type_id
                WHERE r.fee IS NOT NULL
                GROUP BY v.vehicle_type_id
                ";
            }

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

    public function checkClientVehicleLimit($uid)
    {
        try {
            $query = "
            SELECT COUNT(*) as count
            FROM " . self::TABLE . "
            WHERE uid = ?
            ";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param('i', $uid);

            $results = $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $count = $row['count'];

            return [
                'status' => $results,
                'message' => 'Vehicle counts fetched successfully',
                'results' => [
                    'isLimit' => $count >= self::VEHICLE_LIMIT,
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
            $results = FileModel::getInstance()->uploadFile($uid, 2, $vehicle_document, $db_vehicle_id);

            return [
                'status' => $results,
                'message' => $results
                    ? "Vehicle $plate_number added successfully!"
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

    public function deleteVehicle($vehicle_id)
    {
        try {
            $query = "
            DELETE FROM " . self::TABLE . "
            WHERE vehicle_id = $vehicle_id
            ";

            $results = $this->connect->query($query);

            return [
                'status' => $results,
                'message' => $results
                    ? 'Deleted vehicle successfully!'
                    : 'Deleting vehicle failed!',
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

    public function createNewVehicleType($new_vehicle_type)
    {
        try {
            $query = "
            INSERT INTO " . self::TABLE_VEHICLE_TYPES . " (vehicle_type)
            VALUES (?)
            ";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param('s', $new_vehicle_type);

            $results = $stmt->execute();

            return [
                'status' => $results,
                'message' => $results
                    ? "Vehicle $new_vehicle_type added successfully!"
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

    public function deleteVehicleType($vehicle_type_id)
    {
        try {
            $query = "
            DELETE FROM " . self::TABLE . "
            WHERE vehicle_type_id = $vehicle_type_id
            ";

            $results = $this->connect->query($query);

            return [
                'status' => $results,
                'message' => $results
                    ? 'Deleted vehicle type successfully!'
                    : 'Deleting vehicle type failed!',
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
