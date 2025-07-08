<?php 

class locationModel {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function checklocation($user_id) {
        $query = "SELECT * FROM user_location WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function storeLocation($user_id, $latitude, $longitude, $provice, $city, $barangay) {
        $query = "INSERT INTO user_location (user_id, latitude, longitude, province, city, barangay) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iddsss', $user_id, $latitude, $longitude, $provice, $city, $barangay);
        return $stmt->execute();
    }

    public function updateLocation($user_id, $latitude, $longitude, $provice, $city, $barangay) {
        $query= "UPDATE user_location SET latitude = ?, longitude = ?, province = ?, city = ?, barangay = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ddsssi', $latitude, $longitude, $provice, $city, $barangay, $user_id);
        return $stmt->execute();
    }


}

?>