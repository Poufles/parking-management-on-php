<?php
session_start();
require_once "auth.php";
require_once "otp.php";

$auth = new Auth();
$otpObj = new OTP();

// Reset OTP flag on fresh load unless just verified
if (!isset($_POST['verify_otp']) && !isset($_POST['register'])) {
    unset($_SESSION['otp_verified']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Step 1: Send OTP
    if (isset($_POST['send_otp'])) {
        $email = $_POST['email'];
        $otpObj->generate($email);
        echo "OTP sent to $email";
    }

    // Step 2: Verify OTP
    if (isset($_POST['verify_otp'])) {
        if ($otpObj->verify($_POST['otp'])) {
            $_SESSION['otp_verified'] = true;
            echo "OTP verified! You can now complete registration.";
        } else {
            unset($_SESSION['otp_verified']); // reset if invalid
            echo "Invalid or expired OTP.";
        }
    }

    // Step 3: Register user (only if OTP verified)
    if (isset($_POST['register']) && !empty($_SESSION['otp_verified'])) {
        $licenseFile = $_FILES['license']['name'];
        $target = "uploads/" . basename($licenseFile);
        move_uploaded_file($_FILES['license']['tmp_name'], $target);

        if ($auth->register($_POST['name'], $_POST['username'], $_SESSION['otp_email'],
                            $_POST['gender'], $_POST['phone'], $_POST['password'], $licenseFile)) {
            echo "Registration successful!";
            unset($_SESSION['otp_verified']);
        } else {
            echo "Error during registration.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Register - Parcheggiamo</title></head>
<body>
    <h2>Register</h2>
    <form method="post" enctype="multipart/form-data">
        <!-- Always visible -->
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo OTP::Prefill('email'); ?>" required>
        <button type="submit" name="send_otp">Send OTP</button><br><br>

        <label>Enter OTP:</label>
        <input type="text" name="otp" value="<?php echo OTP::Prefill('otp'); ?>">
        <button type="submit" name="verify_otp">Verify OTP</button><br><br>

        <!-- Only visible after OTP verified -->
        <?php if (!empty($_SESSION['otp_verified'])): ?>
            <label>Name:</label>
            <input type="text" name="name" required><br><br>

            <label>Username:</label>
            <input type="text" name="username" required><br><br>

            <label>Gender:</label>
            <select name="gender" required>
                <option value="">Select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select><br><br>

            <label>Phone:</label>
            <input type="text" name="phone" required><br><br>

            <label>Password:</label>
            <input type="password" name="password" required><br><br>

            <label>License File:</label>
            <input type="file" name="license" required><br><br>

            <button type="submit" name="register">Register</button>
        <?php else: ?>
            <p>Verify OTP to unlock other fields.</p>
        <?php endif; ?>
    </form>
</body>
</html>
