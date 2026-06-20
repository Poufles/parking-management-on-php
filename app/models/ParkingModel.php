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
                SELECT SLOT_ID, VEHICLE_ID, TIME_IN, TIME_OUT 
                FROM " . self::TABLE . " 
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

            $stmt = $this->connect->prepare("
                INSERT INTO tbl_payment_history 
                (UID, SLOT_ID, VEHICLE_ID, TIME_IN, TIME_OUT, AMOUNT_TO_PAY, PAYMENT) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "iiisssd",
                $uid,
                $slot_id,
                $slot['VEHICLE_ID'],
                $slot['TIME_IN'],
                $slot['TIME_OUT'],
                $amount_to_pay,
                $payment
            );

            $stmt->execute();
            $history_id = $this->connect->insert_id;
            $stmt->close();

            $stmt = $this->connect->prepare("
                UPDATE tbl_slots 
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

            // Total des blocs
            // $total12hBlocks = ($hours24 * 2) + $hours12;

            // Récupération des tarifs
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
}
