<?php
session_start();

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class OTP {
    public function generate($email) {
        $otp = rand(100000, 999999);
        $_SESSION['otp_email']  = $email;
        $_SESSION['otp_code']   = $otp;
        $_SESSION['otp_expiry'] = time() + 300; 

        
        setcookie("email", $email, time() + 300, "/");
        setcookie("otp", $otp, time() + 300, "/");

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'agustin.larissajuliana@ue.edu.ph';   
            $mail->Password   = 'vbtndfwbwqbczfog';       
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('youraddress@gmail.com', 'Parcheggiamo OTP');
            $mail->addAddress($email); 
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "Your OTP is: $otp";

            $mail->send();
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }

        return $otp;
    }

    public function verify($otp) {
        if (isset($_SESSION['otp_code']) && $otp == $_SESSION['otp_code'] && time() < $_SESSION['otp_expiry']) {
            $_SESSION['otp_verified'] = true;
            return true;
        }
        return false;
    }

    public static function Prefill($field) {
        return isset($_COOKIE[$field]) ? htmlspecialchars($_COOKIE[$field]) : '';
    }
}
?>
