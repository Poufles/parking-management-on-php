<?php

define('DB_CONNECT', new mysqli('localhost', 'root', '', 'DB_PARCHEGGIAMO'));

// class AccountModel
// {
//     private static ?AccountModel $instance = null;
//     private mysqli $connect;
//     public const TABLE = 'tbl_accounts';

//     private function __construct()
//     {
//         $this->connect = DB_CONNECT;
//     }

//     public static function getInstance(): AccountModel
//     {
//         if (self::$instance === null) {
//             self::$instance = new AccountModel();
//         }

//         return self::$instance;
//     }

//     public function searchAccounts($filterBy, $search, $page = 1, $limit = 10)
//     {
//         try {
//             $offset = ($page - 1) * $limit;

//             $allowedFilters = ['name', 'username', 'email_address', 'phone', 'gender'];

//             $where = "";
//             $params = [];
//             $types = "";

//             if (!empty($search)) {

//                 $searchInject = "%" . $search . "%";

//                 if (!empty($filterBy) && in_array($filterBy, $allowedFilters)) {
//                     $where = "WHERE $filterBy LIKE ?";
//                     $params[] = $searchInject;
//                     $types .= "s";
//                 } else {
//                     $where = "WHERE name LIKE ? 
//                           OR username LIKE ? 
//                           OR email_address LIKE ?";

//                     $params = [$searchInject, $searchInject, $searchInject];
//                     $types .= "sss";
//                 }
//             }

//             // MAIN QUERY
//             $query = "
//             SELECT *
//             FROM tbl_accounts
//             $where
//             LIMIT ? OFFSET ?
//         ";

//             $params[] = $limit;
//             $params[] = $offset;
//             $types .= "ii";

//             $stmt = $this->connect->prepare($query);
//             $stmt->bind_param($types, ...$params);

//             $stmt->execute();
//             $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

//             $countQuery = "
//             SELECT COUNT(*) as total
//             FROM tbl_accounts
//             $where
//             ";

//             $countStmt = $this->connect->prepare($countQuery);

//             $countParams = array_slice($params, 0, -2);
//             $countTypes = substr($types, 0, -2);

//             if (!empty($countParams)) {
//                 $countStmt->bind_param($countTypes, ...$countParams);
//             }

//             $countStmt->execute();
//             $total = $countStmt->get_result()->fetch_assoc()['total'];

//             return [
//                 'status' => true,
//                 'message' => 'Accounts fetched successfully',
//                 'results' => [
//                     'rows' => $rows,
//                     'page' => $page,
//                     'limit' => $limit,
//                     'totalPages' => ceil($total / $limit),
//                     'totalItems' => $total
//                 ]
//             ];
//         } catch (Exception $err) {
//             return [
//                 'status' => false,
//                 'message' => $err->getMessage(),
//                 'results' => []
//             ];
//         }
//     }

//     public function loginAccount($username, $password)
//     {
//         try {
//             $query = "
//             SELECT uid, username, password, account_type
//             FROM " . self::TABLE . "
//             WHERE username = ?
//             ";

//             $stmt = $this->connect->prepare($query);
//             $stmt->bind_param("s", $username);

//             $success = $stmt->execute();
//             $results = $stmt->get_result();
//             $row = $results->fetch_assoc();

//             return [
//                 'status' => $success,
//                 'message' => $success
//                     ? 'Created an account successfully'
//                     : 'Failed to create account',
//                 'results' => [
//                     'uid' => $this->connect->insert_id,
//                     'username' => $row['username'],
//                     'account_type' => $row['account_type'],
//                 ]
//             ];
//         } catch (Exception $err) {
//             return [
//                 'status' => false,
//                 'message' => $err->getMessage(),
//                 'results' => []
//             ];
//         }
//     }

//     public function createAccount($name, $username, $email_address, $gender, $phone, $password, $account_type = 'client', $licence = null)
//     {
//         try {
//             $query = "
//             INSERT INTO " . self::TABLE . "
//             (name, username, email_address, gender, phone, password, account_type, licence)
//             VALUES
//             (?, ?, ?, ?, ?, ?, ?, ?)
//             ";

//             $stmt = $this->connect->prepare($query);

//             $stmt->bind_param(
//                 "ssssssss",
//                 $name,
//                 $username,
//                 $email_address,
//                 $gender,
//                 $phone,
//                 $password,
//                 $account_type,
//                 $licence
//             );

//             $success = $stmt->execute();

//             return [
//                 'status' => $success,
//                 'message' => $success
//                     ? 'Created an account successfully'
//                     : 'Failed to create account',
//                 'results' => [
//                     'uid' => $this->connect->insert_id,
//                     'username' => $username,
//                     'account_type' => $account_type,
//                 ]
//             ];
//         } catch (Exception $err) {
//             return [
//                 'status' => false,
//                 'message' => $err->getMessage(),
//                 'results' => []
//             ];
//         };
//     }

//     public function editAccount($uid, $name, $username, $email_address, $gender, $phone)
//     {
//         try {
//             $query = "
//             UPDATE " . self::TABLE . "
//             SET
//                 name = ?,
//                 username = ?,
//                 email_address = ?,
//                 gender = ?,
//                 phone = ?
//             WHERE uid = ?   
//             ";

//             $stmt = $this->connect->prepare($query);

//             $stmt->bind_param(
//                 "sssssi",
//                 $name,
//                 $username,
//                 $email_address,
//                 $gender,
//                 $phone,
//                 $uid
//             );

//             $status = $stmt->execute();

//             return [
//                 'status' => $status && $stmt->affected_rows >= 0,
//                 'message' => $status
//                     ? "Edited $username successfully"
//                     : "Edit failed",
//                 'results' => [
//                     'uid' => $uid,
//                     'username' => $username,
//                     'rows_affected' => $stmt->affected_rows
//                 ]
//             ];
//         } catch (Exception $err) {
//             return [
//                 'status' => false,
//                 'message' => $err->getMessage(),
//                 'results' => []
//             ];
//         };
//     }

//     public function editAccountPassword($uid, $newPassword)
//     {
//         try {
//             $query = "
//             UPDATE " . self::TABLE . "
//             SET password = ?
//             WHERE uid = ?   
//             ";

//             $stmt = $this->connect->prepare($query);

//             $stmt->bind_param(
//                 "si",
//                 $newPassword,
//                 $uid
//             );

//             $status = $stmt->execute();

//             return [
//                 'status' => $status && $stmt->affected_rows >= 0,
//                 'message' => $status
//                     ? "Edited password successfully"
//                     : "Edit failed",
//                 'results' => [
//                     'uid' => $uid,
//                     'rows_affected' => $stmt->affected_rows
//                 ]
//             ];
//         } catch (Exception $err) {
//             return [
//                 'status' => false,
//                 'message' => $err->getMessage(),
//                 'results' => []
//             ];
//         }
//     }

//     public function deleteAccount($uid)
//     {
//         try {
//             $query = "
//             DELETE
//             FROM " . self::TABLE . "
//             WHERE uid = ?
//             ";

//             $stmt = $this->connect->prepare($query);

//             $stmt->bind_param(
//                 "i",
//                 $uid
//             );

//             $stmt->execute();

//             $status = $stmt->affected_rows > 0;

//             return [
//                 'status' => $status,
//                 'message' => $status
//                     ? "Deleted UID: $uid successfully"
//                     : "No account found for UID: $uid",
//                 'results' => [
//                     'uid' => $uid
//                 ]
//             ];
//         } catch (Exception $err) {
//             return [
//                 'status' => false,
//                 'message' => $err->getMessage(),
//                 'results' => []
//             ];
//         };
//     }

//     public function uploadLicence($uid, $licence)
//     {
//         try {
//             $query = "
//             UPDATE " . self::TABLE . "
//             SET licence = ?
//             WHERE uid = ?
//             ";

//             $stmt = $this->connect->prepare($query);

//             $stmt->bind_param('si', $licence, $uid);

//             $status = $stmt->execute();

//             return [
//                 'status' => $status && $stmt->affected_rows >= 0,
//                 'message' => $status
//                     ? "Licence updated successfully for UID: $uid"
//                     : "Update failed for UID: $uid",
//                 'results' => [
//                     'uid' => $uid
//                 ]
//             ];
//         } catch (Exception $err) {
//             return [
//                 'status' => false,
//                 'message' => $err->getMessage(),
//                 'results' => []
//             ];
//         }
//     }
// }


// class VehicleModel
// {
//     private static ?VehicleModel $instance = null;
//     private mysqli $connect;
//     public const TABLE = 'tbl_vehicles';

//     private function __construct()
//     {
//         $this->connect = DB_CONNECT;
//     }

//     public static function getInstance(): VehicleModel
//     {
//         if (self::$instance === null) {
//             self::$instance = new VehicleModel();
//         }

//         return self::$instance;
//     }

//     public function searchVehicles($filterBy, $search, $page = 1, $limit = 10)
//     {
//         try {
//             $offset = ($page - 1) * $limit;

//             $allowedFilters = [
//                 'username',
//                 'plate_number',
//                 'vehicle_type'
//             ];

//             $where = "";
//             $params = [];
//             $types = "";

//             if (!empty($search)) {

//                 $searchInject = "%" . $search . "%";

//                 if (!empty($filterBy) && in_array($filterBy, $allowedFilters)) {

//                     switch ($filterBy) {
//                         case 'username':
//                             $column = 'a.username';
//                             break;

//                         case 'plate_number':
//                             $column = 'v.plate_number';
//                             break;

//                         case 'vehicle_type':
//                             $column = 'vt.vehicle_type';
//                             break;
//                     }

//                     $where = "WHERE {$column} LIKE ?";
//                     $params[] = $searchInject;
//                     $types .= "s";
//                 } else {

//                     $where = "
//                 WHERE a.username LIKE ?
//                 OR v.plate_number LIKE ?
//                 OR vt.vehicle_type LIKE ?
//                 ";

//                     $params = [
//                         $searchInject,
//                         $searchInject,
//                         $searchInject
//                     ];

//                     $types .= "sss";
//                 }
//             }

//             $query = "
//             SELECT
//                 v.vehicle_id as vehicle_id,
//                 a.uid as uid,
//                 a.name as name,
//                 v.plate_number as plate_number,
//                 vt.vehicle_type as vehicle_type
//             FROM ". self::TABLE ." v
//             INNER JOIN ". AccountModel::getInstance()::TABLE ." a
//                 ON v.uid = a.uid
//             INNER JOIN tbl_vehicle_types vt
//                 ON v.vehicle_type_id = vt.vehicle_type_id
//             $where
//             LIMIT ? OFFSET ?
//             ";

//             $params[] = $limit;
//             $params[] = $offset;
//             $types .= "ii";

//             $stmt = $this->connect->prepare($query);
//             $stmt->bind_param($types, ...$params);

//             $stmt->execute();
//             $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

//             // Count query
//             $countQuery = "
//             SELECT COUNT(*) AS total
//             FROM tbl_vehicles v
//             INNER JOIN ". AccountModel::getInstance()::TABLE ." a
//                 ON v.uid = a.uid
//             INNER JOIN tbl_vehicle_types vt
//                 ON v.vehicle_type_id = vt.vehicle_type_id
//             $where
//             ";

//             $countStmt = $this->connect->prepare($countQuery);

//             $countParams = array_slice($params, 0, -2);
//             $countTypes = substr($types, 0, -2);

//             if (!empty($countParams)) {
//                 $countStmt->bind_param($countTypes, ...$countParams);
//             }

//             $countStmt->execute();
//             $total = $countStmt->get_result()->fetch_assoc()['total'];

//             return [
//                 'status' => true,
//                 'message' => 'Vehicles fetched successfully',
//                 'results' => [
//                     'rows' => $rows,
//                     'page' => $page,
//                     'limit' => $limit,
//                     'totalPages' => ceil($total / $limit),
//                     'totalItems' => $total
//                 ]
//             ];
//         } catch (Exception $err) {
//             return [
//                 'status' => false,
//                 'message' => $err->getMessage(),
//                 'results' => []
//             ];
//         }
//     }
// }

// class ParkingModel
// {
//     private static ?ParkingModel $instance = null;
//     private mysqli $connect;
//     public const TABLE = 'tbl_slots';

//     private function __construct()
//     {
//         $this->connect = DB_CONNECT;
//     }

//     public static function getInstance(): ParkingModel
//     {
//         if (self::$instance === null) {
//             self::$instance = new ParkingModel();
//         }

//         return self::$instance;
//     }

//     public function searchParkingSlots($filterBy, $search, $page = 1, $limit = 10)
//     {
//         try {
//             $offset = ($page - 1) * $limit;

//             $allowedFilters = [
//                 'slot_id',
//                 'plate_number',
//                 'username',
//                 'vehicle_type'
//             ];

//             $where = "";
//             $params = [];
//             $types = "";

//             if (!empty($search)) {
//                 $searchInject = "%" . $search . "%";

//                 if (!empty($filterBy) && in_array($filterBy, $allowedFilters)) {
//                     switch ($filterBy) {
//                         case 'slot_id':
//                             $column = 's.slot_id';
//                             break;
//                         case 'plate_number':
//                             $column = 'v.plate_number';
//                             break;
//                         case 'username':
//                             $column = 'a.username';
//                             break;
//                         case 'vehicle_type':
//                             $column = 'vt.vehicle_type';
//                             break;
//                     }

//                     $where = "WHERE {$column} LIKE ?";
//                     $params[] = $searchInject;
//                     $types .= "s";
//                 } else {
//                     $where = "
//                     WHERE s.slot_id LIKE ?
//                        OR v.plate_number LIKE ?
//                        OR a.username LIKE ?
//                        OR vt.vehicle_type LIKE ?
//                 ";

//                     $params = [
//                         $searchInject,
//                         $searchInject,
//                         $searchInject,
//                         $searchInject
//                     ];
//                     $types .= "ssss";
//                 }
//             }

//             $query = "
//             SELECT
//                 CONCAT(s.level, ' - ', s.section, s.slot_number) as slot_id,
//                 s.level,
//                 s.section,
//                 s.slot_number,
//                 s.vehicle_id,
//                 s.time_in,
//                 s.time_out,
//                 v.plate_number,
//                 a.username,
//                 a.name,
//                 vt.vehicle_type,
//                 CASE 
//                     WHEN s.vehicle_id IS NOT NULL AND s.time_out IS NULL THEN 'occupied'
//                     WHEN s.vehicle_id IS NOT NULL THEN 'completed'
//                     ELSE 'available'
//                 END as status
//             FROM tbl_slots s
//             LEFT JOIN " . VehicleModel::getInstance()::TABLE . " v ON s.vehicle_id = v.vehicle_id
//             LEFT JOIN " . AccountModel::getInstance()::TABLE . " a ON v.uid = a.uid
//             LEFT JOIN tbl_vehicle_types vt ON v.vehicle_type_id = vt.vehicle_type_id
//             $where
//             ORDER BY s.level, s.section, s.slot_number
//             LIMIT ? OFFSET ?
//             ";

//             $params[] = $limit;
//             $params[] = $offset;
//             $types .= "ii";

//             $stmt = $this->connect->prepare($query);
//             $stmt->bind_param($types, ...$params);

//             $stmt->execute();
//             $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

//             $countQuery = "
//             SELECT COUNT(*) AS total
//             FROM tbl_slots s
//             LEFT JOIN " . VehicleModel::getInstance()::TABLE . " v ON s.vehicle_id = v.vehicle_id
//             LEFT JOIN " . AccountModel::getInstance()::TABLE . " a ON v.uid = a.uid
//             LEFT JOIN tbl_vehicle_types vt ON v.vehicle_type_id = vt.vehicle_type_id
//             $where
//             ";

//             $countStmt = $this->connect->prepare($countQuery);

//             $countParams = array_slice($params, 0, -2);
//             $countTypes = substr($types, 0, -2);

//             if (!empty($countParams)) {
//                 $countStmt->bind_param($countTypes, ...$countParams);
//             }

//             $countStmt->execute();
//             $total = $countStmt->get_result()->fetch_assoc()['total'];

//             return [
//                 'status' => true,
//                 'message' => 'Parking slots fetched successfully',
//                 'results' => [
//                     'rows' => $rows,
//                     'page' => $page,
//                     'limit' => $limit,
//                     'totalPages' => ceil($total / $limit),
//                     'totalItems' => $total
//                 ]
//             ];
//         } catch (Exception $err) {
//             return [
//                 'status' => false,
//                 'message' => $err->getMessage(),
//                 'results' => []
//             ];
//         }
//     }
// }