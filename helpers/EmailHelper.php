<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

class EmailHelper {
    public function sendOtpEmail($email, $otp) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jonestervilanueva@gmail.com';
            $mail->Password = 'gcyfjlniaaqyvgtl'; // Use an App Password, not your Gmail password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('jonestervilanueva@gmail.com', 'SkilledJob');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Email Verification Code';
            $mail->Body = "<p>Your verification code is: <b>$otp</b></p>";

            if (!$mail->send()) {
                error_log('Mailer Error: ' . $mail->ErrorInfo);
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log('Exception: ' . $e->getMessage());
            return false;
        }
    }
}
