<?php 
class verificationModel {
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }

    public function checkId($id_number) {
        try {
            $query = "SELECT * FROM id_info WHERE id_number = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $id_number);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function saveIdInfo ($user_id, $first_name, $last_name, $middle_name, $suffix, $sex,
                                $birthday, $id_number) {
        try {
            $query = "INSERT INTO id_info (user_id, first_name, last_name, middle_name, suffix, sex, birthday, id_number) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param('isssssss', $user_id, $first_name, $last_name, $middle_name, $suffix, $sex, $birthday, $id_number);
    
            $success = $stmt->execute();
            
            if (!$success) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
    
            return $stmt->insert_id;
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            throw $e;
        }
    }

    public function saveIdPath ($user_id, $id_path, $selfie_path) {
        try {
            $query = "INSERT INTO id_image (user_id, id_path, selfie_path) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('iss', $user_id, $id_path, $selfie_path);
            $success = $stmt->execute();

            if (!$success) {
                throw new Exception("Execute failed: " . $stmt->error);
            } else {
                return true;
            }
            
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            throw $e;
        }
    }
}
?>