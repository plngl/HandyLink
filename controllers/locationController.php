<?php
require_once '../config/Database.php';
require_once '../models/locationModel.php';

class locationController{
    private $conn;
    private $locationMdl;

    public function __construct () {
        $db = new Database();
        $this->conn = $db->getConnection();
        $this->locationMdl = new locationModel($this->conn);
    }

    public function saveLocation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            try {
                $response = ['success' => true, 'message' => null];

                $json = file_get_contents("php://input");
                $data = json_decode($json);

                $user_id = $_SESSION['user_id'] ?? 1;
                $lat = $data->lat ?? null;
                $lng = $data->lng ?? null;
                $province = $data->province ?? null;
                $city = $data->city ?? null;
                $barangay = $data->barangay ?? null;

                if (!$user_id || !$lat || !$lng) {
                    echo json_encode(['success' => false, 'error' => 'Invalid input or session']);
                    return;
                }

                $checkLocation = $this->locationMdl->checklocation($user_id);

                if ($checkLocation) {
                    $update = $this->locationMdl->updateLocation($user_id, $lat, $lng, $province, $city, $barangay);

                    if (!$update) {
                        echo json_encode(['success' => false, 'error' => 'Location cant be Update']);
                        return;
                    }

                    echo json_encode(['success' => true, 'message' => 'Location Update']);
                    return;
                }

                $result = $this->locationMdl->storeLocation($user_id, $lat, $lng, $province, $city, $barangay);

                if (!$result) {
                    echo json_encode(['success' => false, 'error' => 'User Location failed to store.']);
                    return;
                }

                echo json_encode(['success' => true, 'message' => 'User Location Successful']);
            } catch (Exception $e) {
                error_log('Database error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'error' => 'Server error.']);
            }
        }
    }

}
?>