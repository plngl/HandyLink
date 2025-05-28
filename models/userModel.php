<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class userModel {
    private $conn;
    private $otpModel;
    public function __construct($db) {
        $this->conn = $db;
        $this->otpModel = new OtpModel($this->conn);
        
    }

    public function emailExists($email) {
        $query = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function getEmail($user_id) {
        $email = null;
        $query = "SELECT email FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();
        return $email;
    }

    public function registerUser($name, $email, $password){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        $query = "INSERT INTO users (Name, Email, Password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        $stmt->execute();
    
        $user_id = $this->conn->insert_id;

        $_SESSION['user_id'] = $user_id;
    
        $sendOTP = $this->otpModel->generateOtpAndSendEmail($user_id, $email);
    
        if ($stmt && $sendOTP) {
            $response = [
                "success" => true,
                "message" => "Registration successful! Please verify your email.",
                "activation_code" => $sendOTP
            ];
        } else {
            $response = [
                "success" => false,
                "error" => "Something went wrong!"
            ];
        }
    
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>