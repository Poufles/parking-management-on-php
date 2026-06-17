<?php

class AccountModel
{
    private static ?AccountModel $instance = null;
    private mysqli $connect;
    public const TABLE = 'tbl_accounts';

    private function __construct()
    {
        $this->connect = DB_CONNECT;
    }

    public static function getInstance(): AccountModel
    {
        if (self::$instance === null) {
            self::$instance = new AccountModel();
        }

        return self::$instance;
    }

    public function searchAccounts($filterBy, $search, $page = 1, $limit = 10)
    {
        try {
            $offset = ($page - 1) * $limit;

            $allowedFilters = ['name', 'username', 'email_address', 'phone', 'gender'];

            $where = "";
            $params = [];
            $types = "";

            if (!empty($search)) {

                $searchInject = "%" . $search . "%";

                if (!empty($filterBy) && in_array($filterBy, $allowedFilters)) {
                    $where = "WHERE $filterBy LIKE ?";
                    $params[] = $searchInject;
                    $types .= "s";
                } else {
                    $where = "WHERE name LIKE ? 
                          OR username LIKE ? 
                          OR email_address LIKE ?";

                    $params = [$searchInject, $searchInject, $searchInject];
                    $types .= "sss";
                }
            }

            // MAIN QUERY
            $query = "
            SELECT *
            FROM tbl_accounts
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

            $countQuery = "
            SELECT COUNT(*) as total
            FROM tbl_accounts
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
                'message' => 'Accounts fetched successfully',
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

    public function loginAccount($username, $password)
    {
        try {
            $query = "
            SELECT uid, username, password, account_type
            FROM " . self::TABLE . "
            WHERE username = ?
            ";

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param("s", $username);

            $success = $stmt->execute();
            $results = $stmt->get_result();
            $row = $results->fetch_assoc();

            return [
                'status' => $success,
                'message' => $success
                    ? 'Created an account successfully'
                    : 'Failed to create account',
                'results' => [
                    'uid' => $this->connect->insert_id,
                    'username' => $row['username'],
                    'account_type' => $row['account_type'],
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

    public function createAccount($name, $username, $email_address, $gender, $phone, $password, $account_type = 'client', $licence = null)
    {
        try {
            $query = "
            INSERT INTO " . self::TABLE . "
            (name, username, email_address, gender, phone, password, account_type, licence)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = $this->connect->prepare($query);

            $stmt->bind_param(
                "ssssssss",
                $name,
                $username,
                $email_address,
                $gender,
                $phone,
                $password,
                $account_type,
                $licence
            );

            $success = $stmt->execute();

            return [
                'status' => $success,
                'message' => $success
                    ? 'Created an account successfully'
                    : 'Failed to create account',
                'results' => [
                    'uid' => $this->connect->insert_id,
                    'username' => $username,
                    'account_type' => $account_type,
                ]
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage(),
                'results' => []
            ];
        };
    }

    public function editAccount($uid, $name, $username, $email_address, $gender, $phone)
    {
        try {
            $query = "
            UPDATE " . self::TABLE . "
            SET
                name = ?,
                username = ?,
                email_address = ?,
                gender = ?,
                phone = ?
            WHERE uid = ?   
            ";

            $stmt = $this->connect->prepare($query);

            $stmt->bind_param(
                "sssssi",
                $name,
                $username,
                $email_address,
                $gender,
                $phone,
                $uid
            );

            $status = $stmt->execute();

            return [
                'status' => $status && $stmt->affected_rows >= 0,
                'message' => $status
                    ? "Edited $username successfully"
                    : "Edit failed",
                'results' => [
                    'uid' => $uid,
                    'username' => $username,
                    'rows_affected' => $stmt->affected_rows
                ]
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage(),
                'results' => []
            ];
        };
    }

    public function editAccountPassword($uid, $newPassword)
    {
        try {
            $query = "
            UPDATE " . self::TABLE . "
            SET password = ?
            WHERE uid = ?   
            ";

            $stmt = $this->connect->prepare($query);

            $stmt->bind_param(
                "si",
                $newPassword,
                $uid
            );

            $status = $stmt->execute();

            return [
                'status' => $status && $stmt->affected_rows >= 0,
                'message' => $status
                    ? "Edited password successfully"
                    : "Edit failed",
                'results' => [
                    'uid' => $uid,
                    'rows_affected' => $stmt->affected_rows
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

    public function deleteAccount($uid)
    {
        try {
            $query = "
            DELETE
            FROM " . self::TABLE . "
            WHERE uid = ?
            ";

            $stmt = $this->connect->prepare($query);

            $stmt->bind_param(
                "i",
                $uid
            );

            $stmt->execute();

            $status = $stmt->affected_rows > 0;

            return [
                'status' => $status,
                'message' => $status
                    ? "Deleted UID: $uid successfully"
                    : "No account found for UID: $uid",
                'results' => [
                    'uid' => $uid
                ]
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage(),
                'results' => []
            ];
        };
    }

    public function uploadLicence($uid, $licence)
    {
        try {
            $query = "
            UPDATE " . self::TABLE . "
            SET licence = ?
            WHERE uid = ?
            ";

            $stmt = $this->connect->prepare($query);

            $stmt->bind_param('si', $licence, $uid);

            $status = $stmt->execute();

            return [
                'status' => $status && $stmt->affected_rows >= 0,
                'message' => $status
                    ? "Licence updated successfully for UID: $uid"
                    : "Update failed for UID: $uid",
                'results' => [
                    'uid' => $uid
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
