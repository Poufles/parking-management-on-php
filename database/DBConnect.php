<?php

define('DB_CONNECT', new mysqli('localhost', 'root', '', 'DB_PARCHEGGIAMO'));

// class AccountModel {
//     private static ?AccountModel $instance = null;
//     private mysqli $connect;
//     private const TABLE = 'tbl_accounts'; 

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

// public function searchAccounts($filterBy, $search, $page = 1, $limit = 10)
//     {
//         try {
//             $connect = DB_CONNECT;

//             $offset = ($page - 1) * $limit;
//             $searchInject = "$search%";

//             $query = "
//             SELECT *
//             FROM tbl_accounts
//             WHERE " . $filterBy . " like ?
//             LIMIT ? OFFSET ?
//             ";

//             $stmt = $connect->prepare($query);

//             $stmt->bind_param(
//                 "sii",
//                 $searchInject,
//                 $limit,
//                 $offset
//             );

//             $stmt->execute();

//             $results = $stmt->get_result();
//             $rows = $results->fetch_all(MYSQLI_ASSOC);

//             $countQuery = "
//             SELECT COUNT(*) as total
//             FROM tbl_accounts
//             WHERE $filterBy LIKE ?
//             ";

//             $countStmt = $connect->prepare($countQuery);
//             $countStmt->bind_param('s', $searchInject);
//             $countStmt->execute();

//             $total = $countStmt->get_result()->fetch_assoc()['total'];

//             return [
//                 'status' => true,
//                 'message' => 'Searched Account Successfully!',
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
//         };
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
//                 "ssssisss",
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

//     public function editAccount($uid, $name, $username, $email_address, $gender, $phone, $password, $account_type = 'client', $licence = null)
//     {
//         try {
//             $connect = DB_CONNECT;
//             $query = "
//             UPDATE " . self::TABLE . "
//             SET
//                 name = ?,
//                 username = ?,
//                 email_address = ?,
//                 gender = ?,
//                 phone = ?,
//                 password = ?,
//                 account_type = ?,
//                 licence = ?
//             WHERE uid = ?   
//             ";

//             $stmt = $connect->prepare($query);

//             $stmt->bind_param(
//                 "ssssisssi",
//                 $name,
//                 $username,
//                 $email_address,
//                 $gender,
//                 $phone,
//                 $password,
//                 $account_type,
//                 $licence,
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
//                     'account_type' => $account_type,
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

//     public function deleteAccount($uid)
//     {
//         try {
//             $connect = DB_CONNECT;
//             $query = "
//             DELETE
//             FROM " . self::TABLE . "
//             WHERE uid = ?
//             ";

//             $stmt = $connect->prepare($query);

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
//             $connect = DB_CONNECT;
//             $query = "
//             UPDATE " . self::TABLE . "
//             SET licence = ?
//             WHERE uid = ?
//             ";

//             $stmt = $connect->prepare($query);

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

class VehicleService {}
