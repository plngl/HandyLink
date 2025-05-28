<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../helpers/EmailHelper.php';

class OtpModel {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }

    public function generateOtpAndSendEmail($user_id, $email) {
        $verifacation= $this->generateOtpAndCode();

        $otp = $verifacation['otp'];
        $activation_code = $verifacation['activation_code'];
    
        $otp_expiration = date('Y-m-d H:i:s', strtotime('+2 minutes'));
    
        $query = "INSERT INTO otp (user_id, otp, activation_code, otp_expiration) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isss", $user_id, $otp, $activation_code, $otp_expiration);
        $stmt->execute();

        $_SESSION['activation_code'] = $activation_code;
    
        $emailHelper = new EmailHelper();
        $emailHelper->sendOtpEmail($email, $otp);
    
        return $activation_code;
    }

    public function verifyOtp($user_id, $otp) {
        $query = "SELECT * FROM otp WHERE user_id = ? ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $verification = $result->fetch_assoc();
        
        if ($verification) {
            $current_time = date('Y-m-d H:i:s');
            error_log("OTP Expiration: " . $verification['otp_expiration']); // Log expiration
            error_log("Current Time: " . $current_time); // Log current time
    
            if ($verification['otp_expiration'] < $current_time) {
                return ['success' => false, 'error' => 'OTP has expired. Please request a new one.'];
            }
    
            if ($verification['otp'] == $otp) {
                // Mark the user as verified
                $updateQuery = "UPDATE users SET status = 'inactive' WHERE id = ?";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bind_param("i", $user_id);
                $updateStmt->execute();
    
                return ['success' => true, 'message' => 'Email verified successfully!'];
            }
        }
        return ['success' => false, 'error' => 'Invalid OTP. Please try again.'];
    }
    

    public function resendOtp($user_id, $email) {
        $this->generateOtpAndSendEmail($user_id, $email);
        return ['success' => true, 'message' => 'A new OTP has been sent to your email.'];
    }
    
    
    
    private function generateOtpAndCode() {
        $otp = rand(10000, 99999); // Generate 5-digit OTP
        $activation_code = bin2hex(random_bytes(16)); // Generate a random activation code
    
        return [
            'otp' => $otp,
            'activation_code' => $activation_code
        ];
    }
}
?>