<?php
require_once '../config/Database.php';
require_once '../models/serviceModel.php';

class serviceController {
    private $conn;
    private $serviceModel;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->getConnection();
        $this->serviceModel = new ServiceModel($this->conn);
    }

    public function displayService() {
        $services = $this->serviceModel->getAllServices();

        // Safe debug
        if (empty($services)) {
            echo "No services found.";
            return;
        }

        // Send to view
        require_once __DIR__ . '/../views/home.php';
    }
}
?>