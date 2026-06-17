<?php
require_once "db.php";

class Auth {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function register($name, $username, $email, $gender, $phone, $password, $licenseFile) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $type = "user";

        $stmt = $this->db->prepare("INSERT INTO tbl_accounts 
            (Name, Username, Email, Gender, Phone, Password, Type, License) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $name, $username, $email, $gender, $phone, $hash, $type, $licenseFile);
        return $stmt->execute();
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_accounts WHERE Email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['Password'])) {
                $_SESSION['user'] = $row['Name'];
                return true;
            }
        }
        return false;
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        return true;
    }
}
?>
