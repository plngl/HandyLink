<?php
class ServiceModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllServices() {
        $stmt = $this->db->prepare("SELECT * FROM services");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>