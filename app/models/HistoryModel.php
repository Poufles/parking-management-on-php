<?php

class HistoryModel
{
    private static ?HistoryModel $instance = null;
    private mysqli $connect;
    private const TABLE = 'tbl_parking_history';

    private function __construct()
    {
        $this->connect = DB_CONNECT;
    }

    public static function getInstance(): HistoryModel
    {
        if (self::$instance === null) {
            self::$instance = new HistoryModel();
        }

        return self::$instance;
    }

    public function getHistory($page, $limit, $search, $filterDate, $filterType, $filterAcct, $dateFrom, $dateTo, $clientUID = null)
    {
        try {
            $offset = ($page - 1) * $limit;

            $conditions = [];
            $params     = [];
            $types      = '';

            if (!empty($search)) {
                $like = '%' . $search . '%';
                $conditions[] = "(NAME LIKE ? OR PLATE_NUMBER LIKE ? OR VEHICLE_TYPE LIKE ?)";
                $params = array_merge($params, [$like, $like, $like]);
                $types .= 'sss';
            }

            if (!empty($filterDate)) {
                switch ($filterDate) {
                    case 'today':
                        $conditions[] = "DATE(FROM_UNIXTIME(TIME_IN)) = CURDATE()";
                        break;
                    case 'week':
                        $conditions[] = "YEARWEEK(FROM_UNIXTIME(TIME_IN)) = YEARWEEK(CURDATE())";
                        break;
                    case 'month':
                        $conditions[] = "MONTH(FROM_UNIXTIME(TIME_IN)) = MONTH(CURDATE()) 
                                  AND YEAR(FROM_UNIXTIME(TIME_IN)) = YEAR(CURDATE())";
                        break;
                    case 'custom':
                        if (!empty($dateFrom) && !empty($dateTo)) {
                            $conditions[] = "DATE(FROM_UNIXTIME(TIME_IN)) BETWEEN ? AND ?";
                            $params[] = $dateFrom;
                            $params[] = $dateTo;
                            $types .= 'ss';
                        }
                        break;
                }
            }

            if ($clientUID !== null) {
                $conditions[] = "UID = ?";
                $params[] = $clientUID;
                $types .= 'i';
            }

            $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

            $sql = "
            SELECT
                NAME           AS name,
                PLATE_NUMBER   AS plate_number,
                VEHICLE_TYPE   AS vehicle_type,
                TIME_IN        AS time_in,
                TIME_OUT       AS time_out,
                AMOUNT_TO_PAY  AS amount_to_pay,
                PAYMENT        AS payment
            FROM ". self::TABLE ."
            $where
            ORDER BY TIME_IN DESC
            LIMIT ? OFFSET ?
            ";

            $allParams = array_merge($params, [$limit, $offset]);
            $allTypes  = $types . 'ii';

            $stmt = DB_CONNECT->prepare($sql);
            if (!empty($allParams)) {
                $stmt->bind_param($allTypes, ...$allParams);
            }
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            $countSql = "SELECT COUNT(*) AS total FROM tbl_parking_history $where";

            $countStmt = DB_CONNECT->prepare($countSql);
            if (!empty($params)) {
                $countStmt->bind_param($types, ...$params);
            }
            $countStmt->execute();
            $total = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
            $countStmt->close();

            $vtResult = VehicleModel::getInstance()->getAllVehicleTypes();
            $vehicleTypes = $vtResult['results']['rows'] ?? [];

            return [
                'status'  => true,
                'message' => 'History fetched successfully',
                'results' => [
                    'rows'         => $rows,
                    'total'        => (int) $total,
                    'page'         => $page,
                    'limit'        => $limit,
                    'total_pages'  => (int) ceil($total / $limit),
                    'search'       => $search,
                    'filter_date'  => $filterDate,
                    'filter_type'  => $filterType,
                    'filter_acct'  => $filterAcct,
                    'date_from'    => $dateFrom,
                    'date_to'      => $dateTo,
                    'vehicle_types' => $vehicleTypes,
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
