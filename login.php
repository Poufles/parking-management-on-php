<?php
session_start();
require_once "auth.php";

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($auth->login($_POST['email'], $_POST['password'])) {
        header("Location: index.php");
        exit();
    } else {
        echo "Invalid login credentials.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login - Parcheggiamo</title></head>
<body>
    <h2>Login</h2>
    <form method="post">
        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
