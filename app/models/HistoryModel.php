<?php

class HistoryModel
{
    private static ?HistoryModel $instance = null;
    private mysqli $connect;
    private const TABLE = 'tbl_payment_history';

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

    public function getHistoryByUid($uid, $page = 1, $limit = 10)
    {
        try {
            $page = max(1, $page);
            $offset = ($page - 1) * $limit;

            $sql = "SELECT 
                a.NAME AS name,
                v.PLATE_NUMBER AS plate_number,
                vt.VEHICLE_TYPE AS vehicle_type,
                h.TIME_IN AS time_in,
                h.TIME_OUT AS time_out,
                h.AMOUNT_TO_PAY AS amount_to_pay,
                h.PAYMENT AS payment
            FROM " . self::TABLE . " h
            INNER JOIN " . AccountModel::TABLE . " a ON a.UID = h.UID
            INNER JOIN " . VehicleModel::TABLE . " v ON v.VEHICLE_ID = h.VEHICLE_ID
            INNER JOIN " . VehicleModel::TABLE_VEHICLE_TYPES . " vt ON vt.VEHICLE_TYPE_ID = v.VEHICLE_TYPE_ID
            WHERE h.UID = ?
            ORDER BY h.TIME_IN DESC
            LIMIT ? OFFSET ?";

            $stmt = $this->connect->prepare($sql);
            $stmt->bind_param('sii', $uid, $limit, $offset);
            $stmt->execute();

            $result = $stmt->get_result();
            $history = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            $countSql = "SELECT COUNT(*) AS total
                 FROM " . self::TABLE . " h
                 WHERE h.UID = ?";

            $countStmt = $this->connect->prepare($countSql);
            $countStmt->bind_param('s', $uid);
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
