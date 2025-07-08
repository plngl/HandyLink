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

    public function getUserEmail($email){
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function isOnline($user_email) {
        $query = "UPDATE users SET status = 'active' WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $user_email);
        return $stmt->execute();
    }

    public function updateRole($id) {
        $query = "UPDATE users SET role = 'client' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
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

    public function updateProfile($userId, $filename) {
        $query = "UPDATE users SET profile = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $filename, $userId);
        return $stmt->execute();
    }

    public function updateName($user_id, $name) {
        $query = "UPDATE users SET name = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $name, $user_id);
        return $stmt->execute();
    }

    public function verifyEmail ($user_id) {
        try {
            $query = "INSERT INTO verified_email (user_id) VALUES (?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $user_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updatePassword($user_id, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET Password = ? WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $hashed_password, $user_id);
        return $stmt->execute();
    }
}
?>