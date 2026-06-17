<?php

class ParkingModel
{
    private static ?ParkingModel $instance = null;
    private mysqli $connect;
    public const TABLE = 'tbl_slots';

    private function __construct()
    {
        $this->connect = DB_CONNECT;
    }

    public static function getInstance(): ParkingModel
    {
        if (self::$instance === null) {
            self::$instance = new ParkingModel();
        }

        return self::$instance;
    }

    public function searchParkingSlots($filterBy, $search, $page = 1, $limit = 10)
    {
        try {
            $offset = ($page - 1) * $limit;

            $allowedFilters = [
                'slot_id',
                'plate_number',
                'username',
                'vehicle_type'
            ];

            $where = "";
            $params = [];
            $types = "";

            if (!empty($search)) {
                $searchInject = "%" . $search . "%";

                if (!empty($filterBy) && in_array($filterBy, $allowedFilters)) {
                    switch ($filterBy) {
                        case 'slot_id':
                            $column = 's.slot_id';
                            break;
                        case 'plate_number':
                            $column = 'v.plate_number';
                            break;
                        case 'username':
                            $column = 'a.username';
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
                    WHERE s.slot_id LIKE ?
                       OR v.plate_number LIKE ?
                       OR a.username LIKE ?
                       OR vt.vehicle_type LIKE ?
                ";

                    $params = [
                        $searchInject,
                        $searchInject,
                        $searchInject,
                        $searchInject
                    ];
                    $types .= "ssss";
                }
            }

            $query = "
            SELECT
                CONCAT(s.level, ' - ', s.section, s.slot_number) as slot_id,
                s.level,
                s.section,
                s.slot_number,
                s.vehicle_id,
                s.time_in,
                s.time_out,
                v.plate_number,
                a.username,
                a.name,
                vt.vehicle_type,
                CASE 
                    WHEN s.vehicle_id IS NOT NULL AND s.time_out IS NULL THEN 'occupied'
                    WHEN s.vehicle_id IS NOT NULL THEN 'completed'
                    ELSE 'available'
                END as status
            FROM tbl_slots s
            LEFT JOIN " . VehicleModel::getInstance()::TABLE . " v ON s.vehicle_id = v.vehicle_id
            LEFT JOIN " . AccountModel::getInstance()::TABLE . " a ON v.uid = a.uid
            LEFT JOIN tbl_vehicle_types vt ON v.vehicle_type_id = vt.vehicle_type_id
            $where
            ORDER BY s.level, s.section, s.slot_number
            LIMIT ? OFFSET ?
            ";

            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param($types, ...$params);

            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $countQuery = "
            SELECT COUNT(*) AS total
            FROM tbl_slots s
            LEFT JOIN " . VehicleModel::getInstance()::TABLE . " v ON s.vehicle_id = v.vehicle_id
            LEFT JOIN " . AccountModel::getInstance()::TABLE . " a ON v.uid = a.uid
            LEFT JOIN tbl_vehicle_types vt ON v.vehicle_type_id = vt.vehicle_type_id
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
                'message' => 'Parking slots fetched successfully',
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
}
