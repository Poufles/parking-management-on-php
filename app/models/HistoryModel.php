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

    public function getHistory($page = 1, $limit = 10, $name = null)
    {
        try {
            $page = max(1, $page);
            $offset = ($page - 1) * $limit;

            $sql = "SELECT 
                NAME AS name,
                PARKING_SLOT as parking_slot,
                PLATE_NUMBER AS plate_number,
                VEHICLE_TYPE AS vehicle_type,
                TIME_IN AS time_in,
                TIME_OUT AS time_out,
                AMOUNT_TO_PAY AS amount_to_pay,
                PAYMENT AS payment
            FROM " . self::TABLE . "
            ";

            $params = [$limit, $offset];
            $types = 'ii';

            if (isset($uid)) {
                $sql .= "
                WHERE NAME = ?
                ";

                $types = 'i' . $types;
                array_unshift($params, $uid);
            }

            $sql .= "
            ORDER BY TIME_IN DESC
            LIMIT ? OFFSET ?
            ";

            $stmt = $this->connect->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            $result = $stmt->get_result();
            $history = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            $countSql = "
            SELECT COUNT(*) AS total
            FROM " . self::TABLE . "
            ";

            if (isset($uid)) {
                $countSql .= " WHERE UID = ?";

                $countStmt = $this->connect->prepare($countSql);
                $countStmt->bind_param('i', $uid);
            } else {
                $countStmt = $this->connect->prepare($countSql);
            }

            $countStmt->execute();
            $total = $countStmt->get_result()->fetch_assoc()['total'];
            $countStmt->close();

            return [
                'status' => $result,
                'message' => 'Successfully retrieved !',
                'results' => [
                    'rows' => $history,
                    'total' => (int) $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => (int) ceil($total / $limit),
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
