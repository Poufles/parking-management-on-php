<?php

class RateModel
{
    private static ?RateModel $instance = null;
    private mysqli $connect;
    public const TABLE = 'tbl_rates';

    private function __construct()
    {
        $this->connect = DB_CONNECT;
    }

    public static function getInstance(): RateModel
    {
        if (self::$instance === null) {
            self::$instance = new RateModel();
        }

        return self::$instance;
    }

    public function searchRates($filterBy, $search, $page = 1, $limit = 10)
    {
        try {
            $offset = ($page - 1) * $limit;

            $allowedFilters = [
                'rate_id',
                'hours',
                'vehicle_type'
            ];

            $where = "";
            $params = [];
            $types = "";

            if (!empty($search)) {
                $searchInject = "%" . $search . "%";

                if (!empty($filterBy) && in_array($filterBy, $allowedFilters)) {

                    switch ($filterBy) {
                        case 'rate_id':
                            $column = 'r.rate_id';
                            $where = "WHERE {$column} = ?";
                            $params[] = (int)$search;
                            $types .= "i";
                            break;

                        case 'hours':
                            $column = 'h.hours';
                            $where = "WHERE {$column} LIKE ?";
                            $params[] = $searchInject;
                            $types .= "s";
                            break;

                        case 'vehicle_type':
                            $column = 'vt.vehicle_type';
                            $where = "WHERE {$column} LIKE ?";
                            $params[] = $searchInject;
                            $types .= "s";
                            break;
                    }
                } else {
                    // Recherche globale
                    $where = "
                    WHERE r.rate_id = ?
                    OR h.hours LIKE ?
                    OR vt.vehicle_type LIKE ?
                    ";

                    $params = [
                        (int)$search,
                        $searchInject,
                        $searchInject
                    ];
                    $types .= "iss";
                }
            }

            $query = "
            SELECT
                r.rate_id,
                h.hours_id,
                h.hours,
                vt.vehicle_type_id,
                vt.vehicle_type,
                r.fee
            FROM " . self::TABLE . " r
            INNER JOIN " . HoursModel::getInstance()::TABLE . " h 
                ON r.hours_id = h.hours_id
            INNER JOIN " . VehicleModel::getInstance()::TABLE_VEHICLE_TYPES . " vt 
                ON r.vehicle_type_id = vt.vehicle_type_id
            $where
            ORDER BY r.fee DESC, h.hours DESC
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
            FROM " . self::TABLE . " r
            INNER JOIN " . HoursModel::getInstance()::TABLE . " h ON r.hours_id = h.hours_id
            INNER JOIN " . VehicleModel::getInstance()::TABLE_VEHICLE_TYPES . " vt ON r.vehicle_type_id = vt.vehicle_type_id
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
                'message' => 'Rates fetched successfully!',
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

    public function isRateExist($hours, $vehicle_type)
    {
        try {
            $query = "
            SELECT COUNT(*) as count                
            FROM " . self::TABLE . " 
            WHERE hours_id = ?
            AND vehicle_type_id = ?
            ";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param('ii', $hours, $vehicle_type);

            $results = $stmt->execute();
            $isRateExist = $stmt->get_result()->fetch_assoc()['count'];

            return [
                'status' => $results,
                'message' => $results
                    ? 'Rates fetched successfully!'
                    : 'Rates fetched successfully!',
                'results' => [
                    'isRateExist' => $isRateExist,
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

    public function createRateFee($hours_id, $vehicle_type_id, $fee)
    {
        try {
            $query = "
            INSERT INTO " . self::TABLE . " (hours_id, vehicle_type_id, fee)
            VALUES (?, ?, ?)
            ";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param('iii', $hours_id, $vehicle_type_id, $fee);

            $results = $stmt->execute();

            return [
                'status' => $results,
                'message' => $results
                    ? 'Created rate fee successfully!'
                    : 'Creating rate fee failed!',
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

    public function editRateFee($rate_id, $fee)
    {
        try {
            $query = "
            UPDATE " . self::TABLE . "
            SET FEE = ?
            WHERE RATE_ID = ?
            ";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param('ii', $fee, $rate_id);

            $results = $stmt->execute();

            return [
                'status' => $results,
                'message' => $results
                    ? 'Edited rate fee successfully!'
                    : 'Editing rate fee failed!',
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

    public function deleteRateFee($rate_id)
    {
        try {
            $query = "
            DELETE FROM " . self::TABLE . "
            WHERE rate_id = $rate_id
            ";

            $results = $this->connect->query($query);

            return [
                'status' => $results,
                'message' => $results
                    ? 'Deleted rate fee successfully!'
                    : 'Deleting rate fee failed!',
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

    public function getRateFees($vehicleTypeId)
    {
        $query = "
        SELECT hours_id, fee 
        FROM " . self::TABLE . " 
        WHERE vehicle_type_id = ?
        ";

        $stmt = $this->connect->prepare($query);
        $stmt->bind_param("i", $vehicleTypeId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $fees = [];
        foreach ($result as $row) {
            $fees[$row['hours_id']] = (float)$row['fee'];
        }

        return $fees;
    }

    public function getRateFeeInformation($rate_id) {
        try {
            $query = "
            SELECT
                rate_id,
                hours_id,
                vehicle_type_id,
                fee
            FROM ". self::TABLE ."
            WHERE rate_id = $rate_id
            ";

            $results = $this->connect->query($query);
            $rows = $results->fetch_assoc();
            
            return [
                'status' => true,
                'message' => 'Effectuated',
                'response' => [
                    'rows' => $rows
                ]
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage()
            ];
        }
    }

    public function isRateUsed($vehicle_type_id) {
        try {
            $query = "
            SELECT
            r.rate_id as rate_id,
            r.vehicle_type_id as vehicle_type_id,
            s.vehicle_id as vehicle_id
            FROM ". self::TABLE ." as r
            INNER JOIN ". VehicleModel::getInstance()::TABLE_VEHICLE_TYPES ." vt ON r.vehicle_type_id = vt.vehicle_type_id
            INNER JOIN ". VehicleModel::getInstance()::TABLE ." v ON vt.vehicle_type_id = v.vehicle_type_id
            INNER JOIN ". ParkingModel::getInstance()::TABLE ." s ON v.vehicle_id = s.vehicle_id
            WHERE r.vehicle_type_id = ?
            ";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param('i', $vehicle_type_id);
            $stmt->execute();
            $results = $stmt->get_result();
            $count = $results->num_rows;

            return [
                'status' => true,
                'message' => 'Effectuated !',
                'response' => [
                    'isRateUsed' => $count != 0
                ]
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage()
            ];
        }
    }
}
