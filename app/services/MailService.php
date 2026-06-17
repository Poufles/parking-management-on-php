<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-master/src/SMTP.php';

class MailService
{
    private static $instance = null;
    private static $fromEmail = '';
    private static $password = '';
    private static $username = 'Parcheggiamo - Customer Support';

    private function __construct(){}

    public static function getInstance()
    {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    public function sendEmail($toEmail, $recipient_name, $subject, $body, $altBody,)
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       // Set the Gmail SMTP server
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = self::$fromEmail;                       // Your Gmail address
            $mail->Password   = self::$password;                        // Your Google App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
            $mail->Port       = 587;                                    // TCP port to connect to

            // Recipients
            $mail->setFrom(self::$fromEmail, self::$username);
            $mail->addAddress($toEmail, $recipient_name);

            // Content
            $mail->isHTML(true);                                        // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $altBody;

            $status = $mail->send();
            if ($status) return [
                'status' => $status,
                'message' => 'Email sent!'
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $mail->ErrorInfo
            ];
        };
    }
}
