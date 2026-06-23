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

    public function ParkingTableForAdmin($page = 1, $limit = 20)
    {
        try {
            $page   = max(1, (int)$page);
            $limit  = max(1, min(100, (int)$limit));
            $offset = ($page - 1) * $limit;

            $conditions = [];
            $params     = [];
            $types      = "";

            $level   = $_GET['level']   ?? 'L1';
            $section = $_GET['section'] ?? 'A';

            $_SESSION['parking_level']   = $level;
            $_SESSION['parking_section'] = $section;

            if (!empty($level)) {
                $conditions[] = "s.level = ?";
                $params[] = $level;
                $types .= "s";
            }
            if (!empty($section)) {
                $conditions[] = "s.section = ?";
                $params[] = $section;
                $types .= "s";
            }

            $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

            $query = "
            SELECT
                s.slot_id,
                CONCAT(s.level, ' - ', s.section, s.slot_number) as slot,
                s.level, s.section, s.slot_number,
                s.vehicle_id, s.time_in, s.time_out,
                v.plate_number,
                a.name as client_name,
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
            {$where}
            ORDER BY (s.time_out IS NOT NULL) DESC, s.level ASC, s.section ASC, s.slot_number ASC
            LIMIT ? OFFSET ?
            ";

            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Total
            $countQuery = "SELECT COUNT(*) as total FROM tbl_slots s {$where}";
            $countStmt = $this->connect->prepare($countQuery);

            if (!empty($conditions)) {
                $cTypes = substr($types, 0, -2);
                $cParams = array_slice($params, 0, -2);
                $countStmt->bind_param($cTypes, ...$cParams);
            }
            $countStmt->execute();
            $total = $countStmt->get_result()->fetch_assoc()['total'];

            return [
                'status' => true,
                'message' => 'Effectuated',
                'results' => [
                    'rows'           => $rows,
                    'page'           => $page,
                    'limit'          => $limit,
                    'totalPages'     => ceil($total / $limit),
                    'totalItems'     => $total,
                    'currentLevel'   => $level,
                    'currentSection' => $section
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function ParkingTableForClient($uid, $level, $section)
    {

        try {
            $sql = "SELECT 
                    s.SLOT_ID AS slot_id,
                    s.LEVEL AS level,
                    s.SECTION AS section,
                    s.SLOT_NUMBER AS slot_number,
                    s.VEHICLE_ID AS vehicle_id,
                    s.TIME_IN AS time_in,
                    s.TIME_OUT AS time_out,
                    v.UID AS owner_uid,
                    v.PLATE_NUMBER AS plate_number,
                    vt.VEHICLE_TYPE AS vehicle_type
                FROM " . self::TABLE . " s
                LEFT JOIN " . VehicleModel::TABLE . " v ON v.VEHICLE_ID = s.VEHICLE_ID
                LEFT JOIN " . VehicleModel::TABLE_VEHICLE_TYPES . " vt ON vt.VEHICLE_TYPE_ID = v.VEHICLE_TYPE_ID
                WHERE s.LEVEL = ? AND s.SECTION = ?
                ORDER BY s.SLOT_NUMBER ASC
                LIMIT 10
                ";

            $stmt = $this->connect->prepare($sql);
            $stmt->bind_param('ss', $level, $section);
            $stmt->execute();

            $result = $stmt->get_result();
            $slots = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            foreach ($slots as &$slot) {
                if ($slot['vehicle_id'] === null) {
                    $slot['status'] = 'available';
                } elseif ((string) $slot['owner_uid'] === (string) $uid) {
                    $slot['status'] = 'parked';
                } else {
                    $slot['status'] = 'occupied';
                }
            }
            unset($slot);

            return [
                'status' => $result,
                'message' => 'Fetched Slots !',
                'response' => [
                    'slots' => $slots
                ]
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage(),
                'response' => []
            ];
        }
    }

    public function parkIn($slot_id, $vehicle_id)
    {
        try {
            $query = "
            UPDATE " . self::TABLE . "
            SET vehicle_id = ?,
            time_in = ?
            WHERE slot_id = ?
            ";

            $stmt = $this->connect->prepare($query);

            $now = time();

            $stmt->bind_param(
                'iii',
                $vehicle_id,
                $now,
                $slot_id
            );

            $results = $stmt->execute();

            return [
                'status' => $results,
                'message' => 'Parked in !',
                'rows' => []
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage(),
                'response' => []
            ];
        }
    }

    public function searchVehicle($vehicle_id)
    {
        try {
            $query = "
            SELECT *
            FROM " . self::TABLE . "
            WHERE vehicle_id = $vehicle_id
            ";

            $results = $this->connect->query($query, MYSQLI_ASSOC);
            $count = $results->num_rows;
            $row = $results->fetch_assoc();

            return [
                'status' => $results,
                'message' => 'Effectuated !',
                'response' => [
                    'count' => $count,
                    'row' => $row
                ]
            ];
        } catch (Exception $err) {
        }
    }

    public function getCheckoutRequestDetails($slotId)
    {
        try {
            $query = "
            SELECT
                CONCAT(s.level, ' - ', s.section, s.slot_number) as slot,
                a.uid,
                a.name,
                v.plate_number,
                vt.vehicle_type,
                v.vehicle_type_id,
                s.time_in,
                s.time_out,
                s.vehicle_id
            FROM tbl_slots s
            INNER JOIN tbl_vehicles v
                ON s.vehicle_id = v.vehicle_id
            INNER JOIN tbl_accounts a
                ON v.uid = a.uid
            INNER JOIN tbl_vehicle_types vt
                ON v.vehicle_type_id = vt.vehicle_type_id
            WHERE s.slot_id = ?
            LIMIT 1
        ";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param("i", $slotId);
            $stmt->execute();

            $data = $stmt->get_result()->fetch_assoc();

            if (!$data) {
                return [
                    'status' => false,
                    'message' => 'Parking record not found.'
                ];
            }

            $data['fee'] = $this->calculateParkingFee(
                $data['vehicle_type_id'],
                $data['time_in'],
                $data['time_out'],
            );

            unset($data['vehicle_type_id']);

            return [
                'status' => true,
                'message' => 'Effectuated !',
                'results' => [
                    'details' => $data
                ]
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage()
            ];
        }
    }

    public function processPayment($slot_id, $payment, $amount_to_pay, $uid)
    {
        try {
            $stmt = $this->connect->prepare("
            SELECT 
            a.NAME,
            CONCAT(s.LEVEL, ' - ', s.SECTION, s.SLOT_NUMBER) as PARKING_SLOT, 
            v.PLATE_NUMBER,
            vt.VEHICLE_TYPE,
            s.TIME_IN, 
            s.TIME_OUT 
            FROM " . self::TABLE . " s
            INNER JOIN ". VehicleModel::TABLE ." v ON s.VEHICLE_ID = v.VEHICLE_ID
            INNER JOIN ". AccountModel::TABLE ." a ON v.UID = a.UID
            INNER JOIN ". VehicleModel::TABLE_VEHICLE_TYPES ." vt ON v.VEHICLE_TYPE_ID = vt.VEHICLE_TYPE_ID
            WHERE SLOT_ID = ?
            ");
            
            $stmt->bind_param("i", $slot_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $slot = $result->fetch_assoc();
            $stmt->close();
            
            if (!$slot) {
                return [
                    'status'  => false,
                    'message' => 'Slot not found.',
                    'results' => []
                    ];
                    }
                    
                    $this->connect->begin_transaction();
                    var_dump($slot);

            $stmt = $this->connect->prepare("
                INSERT INTO ". HistoryModel::TABLE ." 
                (NAME, PARKING_SLOT, PLATE_NUMBER, VEHICLE_TYPE, TIME_IN, TIME_OUT, AMOUNT_TO_PAY, PAYMENT) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $name = $slot['NAME'];
            $parking_slot = $slot['PARKING_SLOT'];
            $plate_number = $slot['PLATE_NUMBER'];
            $vehicle_type= $slot['VEHICLE_TYPE'];

            $stmt->bind_param(
                "ssssiiii",
                $name,
                $parking_slot,
                $plate_number,
                $vehicle_type,
                $slot['TIME_IN'],
                $slot['TIME_OUT'],
                $amount_to_pay,
                $payment
            );

            $stmt->execute();
            $history_id = $this->connect->insert_id;
            $stmt->close();

            $stmt = $this->connect->prepare("
                UPDATE ". self::TABLE ." 
                SET VEHICLE_ID = NULL,
                    TIME_IN = NULL,
                    TIME_OUT = NULL 
                WHERE SLOT_ID = ?
            ");

            $stmt->bind_param("i", $slot_id);
            $stmt->execute();
            $stmt->close();

            $this->connect->commit();

            $data = [
                'history_id'    => $history_id,
                'slot_id'       => $slot_id,
                'vehicle_id'    => $slot['VEHICLE_ID'],
                'amount_to_pay' => $amount_to_pay,
                'payment'       => $payment,
                'change'        => round($payment - $amount_to_pay, 2),
                'timestamp'     => date('Y-m-d H:i:s')
            ];

            return [
                'status'  => true,
                'message' => '',
                'results' => [
                    'details' => $data
                ]
            ];
        } catch (Exception $err) {
            $this->connect->rollback();

            return [
                'status'  => false,
                'message' => $err->getMessage(),
                'results' => []
            ];
        }
    }
    // public function processPayment($slot_id, $payment, $amount_to_pay, $uid)
    // {
    //     try {
    //         $stmt = $this->connect->prepare("
    //             SELECT SLOT_ID, VEHICLE_ID, TIME_IN, TIME_OUT 
    //             FROM " . self::TABLE . " 
    //             WHERE SLOT_ID = ?
    //         ");

    //         $stmt->bind_param("i", $slot_id);
    //         $stmt->execute();
    //         $result = $stmt->get_result();
    //         $slot = $result->fetch_assoc();
    //         $stmt->close();

    //         if (!$slot) {
    //             return [
    //                 'status'  => false,
    //                 'message' => 'Slot not found.',
    //                 'results' => []
    //             ];
    //         }

    //         $this->connect->begin_transaction();

    //         $stmt = $this->connect->prepare("
    //             INSERT INTO tbl_payment_history 
    //             (UID, SLOT_ID, VEHICLE_ID, TIME_IN, TIME_OUT, AMOUNT_TO_PAY, PAYMENT) 
    //             VALUES (?, ?, ?, ?, ?, ?, ?)
    //         ");

    //         $stmt->bind_param(
    //             "iiisssd",
    //             $uid,
    //             $slot_id,
    //             $slot['VEHICLE_ID'],
    //             $slot['TIME_IN'],
    //             $slot['TIME_OUT'],
    //             $amount_to_pay,
    //             $payment
    //         );

    //         $stmt->execute();
    //         $history_id = $this->connect->insert_id;
    //         $stmt->close();

    //         $stmt = $this->connect->prepare("
    //             UPDATE tbl_slots 
    //             SET VEHICLE_ID = NULL,
    //                 TIME_IN = NULL,
    //                 TIME_OUT = NULL 
    //             WHERE SLOT_ID = ?
    //         ");

    //         $stmt->bind_param("i", $slot_id);
    //         $stmt->execute();
    //         $stmt->close();

    //         $this->connect->commit();

    //         $data = [
    //             'history_id'    => $history_id,
    //             'slot_id'       => $slot_id,
    //             'vehicle_id'    => $slot['VEHICLE_ID'],
    //             'amount_to_pay' => $amount_to_pay,
    //             'payment'       => $payment,
    //             'change'        => round($payment - $amount_to_pay, 2),
    //             'timestamp'     => date('Y-m-d H:i:s')
    //         ];

    //         return [
    //             'status'  => true,
    //             'message' => '',
    //             'results' => [
    //                 'details' => $data
    //             ]
    //         ];
    //     } catch (Exception $err) {
    //         $this->connect->rollback();

    //         return [
    //             'status'  => false,
    //             'message' => $err->getMessage(),
    //             'results' => []
    //         ];
    //     }
    // }

    private function calculateParkingFee($vehicleTypeId, $timeIn, $timeOut = null)
    {
        try {
            $timeInDate  = new DateTime("@" . $timeIn);
            $timeOutDate = $timeOut ? new DateTime("@" . $timeOut) : new DateTime(); // maintenant

            $interval   = $timeInDate->diff($timeOutDate);
            $totalHours = ($interval->days * 24) + $interval->h + ($interval->i / 60);

            $totalHoursCeiled = ceil($totalHours);

            $hours24 = floor($totalHoursCeiled / 24);
            $remainingHours = $totalHoursCeiled % 24;

            $hours12 = 0;
            if ($remainingHours > 0) {
                $hours12 = ($remainingHours <= 12) ? 1 : 2;
            }

            $fees = RateModel::getInstance()->getRateFees($vehicleTypeId);

            $totalFee = 0;
            $details = [];

            if ($hours24 > 0) {
                $fee24 = $fees[2] ?? 0; // hours_id = 2
                $totalFee += $hours24 * $fee24;
                $details[] = "$hours24 × 24h ({$fee24} Fc)";
            }

            if ($hours12 > 0) {
                $fee12 = $fees[1] ?? 0; // hours_id = 1
                $totalFee += $hours12 * $fee12;
                $details[] = "$hours12 × 12h ({$fee12} Fc)";
            }

            return [
                'status' => true,
                'message' => "Success",
                'results' => [
                    'fee' => $totalFee,
                    'total_hours' => round($totalHours, 2),
                    'total_hours_ceiled' => $totalHoursCeiled,
                    'blocks_24h' => $hours24,
                    'blocks_12h' => $hours12,
                    'details' => implode(" + ", $details)
                ]
            ];
        } catch (Exception $e) {
            return [
                'status'  => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function isVehicleAlreadyParked($vehicle_id)
    {
        try {
            $query = "
            SELECT COUNT(*) as count
            FROM " . self::TABLE . "
            WHERE vehicle_id = $vehicle_id
            ";

            $results = $this->connect->query($query);
            $rows = $results->fetch_all(MYSQLI_ASSOC);
            $row = $rows[0];
            $count = $row['count'];

            return $count != 0;
        } catch (Exception $err) {
            return [
                'status'  => false,
                'message' => $err->getMessage()
            ];
        }
    }

    public function requestTimeOut($slot_id)
    {
        try {
            $query = "
            UPDATE " . self::TABLE . "
            SET time_out = ?
            WHERE slot_id = ?
            ";

            $stmt = $this->connect->prepare($query);

            $time_out = time();

            $stmt->bind_param('ii', $time_out, $slot_id);
            $stmt->execute();

            return [
                'status' => true,
                'message' => 'Time Out request',
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage()
            ];
        }
    }
}
