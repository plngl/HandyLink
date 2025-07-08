<?php
require_once '../config/Database.php';
require_once '../models/verificationModel.php';
require_once '../models/userModel.php';

class verificationController {
    private $conn;
    private $verifyModel;
    private $usrModel;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        $this->verifyModel = new verificationModel($this->conn);
        $this->usrModel = new userModel($this->conn);
    }

    public function checkId() {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $response = ["success" => false, "messages" => [], "id_path" => "", "selfie_path" => ""];
            
            // Use relative paths for web access
            $id_dir = __DIR__ . '/../images/id/';  
            $selfie_dir = __DIR__ . '/../images/selfie';
            $web_path = 'images/id/';
    
            // Ensure the directory exists
            if (!file_exists($id_dir) && !file_exists($selfie_dir)) {
                mkdir($id_dir, 0777, true);
                mkdir($selfie_dir, 0777, true);
            }
    
            $id_filename = uniqid('id_') . '.jpg';
            $selfie_filename = uniqid('selfie_') . '.jpg';
            
            $id_path = $id_dir . '/' . $id_filename;
            $selfie_path = $selfie_dir . '/' . $selfie_filename;
            
            // Store web-accessible paths in response
            $response['id_path'] = $web_path . $id_filename;
            $response['selfie_path'] = $web_path . $selfie_filename;
    
            // Process ID photo
            if (!move_uploaded_file($_FILES['id_photo']['tmp_name'], $id_path)) {
                $response['messages'][] = "Failed to upload ID Photo";
                echo json_encode($response);
                exit;
            }
    
            // Process selfie
            $selfie_data = explode(',', $_POST['selfie_data'])[1];
            if (!file_put_contents($selfie_path, base64_decode($selfie_data))) {
                $response['messages'][] = "Failed to upload Selfie";
                echo json_encode($response);
                exit;
            } else {
                $response['success'] = 'success';
                $response['messages'][] = 'ID verification successful';
            }
    
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }        
    }

    public function saveId() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $response = ['success' => false, 'message' => ''];

            $user_role = $_SESSION['role'] ?? '';
            
                $data = [
                        'user_id' => $_POST['user_id'] ?? '',
                        'firstname' => $_POST['firstName'] ?? '',
                        'lastname' => $_POST['lastName'] ?? '',
                        'middle' => $_POST['middleName'] ?? '',
                        'suffix' => $_POST['suffix'] ?? '',
                        'gender' => $_POST['sex'] ?? '',
                        'birthday' => $_POST['birthday'] ?? '',
                        'id_no' => $_POST['idNumber'] ?? '',
                        'id_path' => $_POST['id_path'] ?? '',
                        'selfie_path' => $_POST['selfie_path'] ?? ''
                    ];
        
                    // Validate required fields
                    $required = ['user_id', 'firstname', 'lastname', 'gender', 'birthday', 'id_no', 'id_path', 'selfie_path'];
                    foreach ($required as $field) {
                        if (empty($data[$field])) {
                            throw new Exception("Missing required field: $field");
                            $response['success'] = false;
                            $response['message'] = 'empty required field';
                        }
                    }

                    if ($this->verifyModel->checkId($data['id_no']) && isset($user_role)) {
                        $response['success'] = false;
                        $response['message'] = 'This id is already used for verification';
                        echo json_encode($response);
                        exit;
                    }
        
                    $saveInfo = $this->verifyModel->saveIdInfo(
                        $data['user_id'],
                        $data['firstname'],
                        $data['lastname'],
                        $data['middle'],
                        $data['suffix'],
                        $data['gender'],
                        $data['birthday'],
                        $data['id_no'],
                    );

                    if ($saveInfo === false) {
                        throw new Exception("Failed to save ID information to database");
                        $response['success'] = false;
                        $response['message'] = 'cant save info';
                    }

                    $saveImagePath = $this->verifyModel->saveIdPath($data['user_id'], $data['id_path'], $data['selfie_path']);

                    if ($saveImagePath === false) {
                        throw new Exception("Failed to save image paths to database");
                        $response['success'] = false;
                        $response['message'] = 'cant save iage path';
                    }

                    $updateUserRole = $this->usrModel->updateRole($data['user_id']);

                    if ($updateUserRole) {
                        $response['success'] = false;
                        $response['message'] = 'cant update role';
                    }
                
                if ($saveInfo && $saveImagePath) {
                    $response['success'] = true;
                    $response['message'] = 'ID information saved successfully';
                } else {
                    $response['message'] = 'Failed to save ID information';
                }

            echo json_encode($response);
        }
    }

    public function validateTesdaCertificate() {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $response = ["success" => false, "message" => ""];
            $tesdaFile = $_FILES['tesda_certificate'];
            $upload_dir = __DIR__ . '/../images/tesda/';
        
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if ($tesdaFile['error'] != 0) {
                $response["error"] = "Failed to upload file";
                echo json_encode($response);
                return;
            }
    
            $tesda_name = $tesdaFile['name'];
            $tesda_path = $upload_dir . $tesda_name;
    
            if (!move_uploaded_file($tesdaFile['tmp_name'], $tesda_path)) {
                $response["error"] = "Failed to save file";
                echo json_encode($response);
                return;
            }
    
            $tesdaText = $this->extractTextFromImage($tesda_path);
    
            if (!preg_match('/\bNATIONAL\sCERTIFICATE\b/i', $tesdaText)) {
                $response["error"] = "TESDA Certificate is invalid";
                echo json_encode($response);
                return;
            }
    
            $response = [
                "success" => true, 
                "message" => "Documents successfully verified.",
                "file_path" => $tesda_path
            ];
            echo json_encode($response);
        }
    }

    function extractTextFromImage($image_path) {
        $ocr_api_url = "https://api.ocr.space/parse/image";

        $ch = curl_init();

        $post_fields = [
            'file' => new CURLFile($image_path),
            'apikey' => 'K83075816088957'
        ];
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $ocr_api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post_fields,
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        return isset($data['ParsedResults'][0]['ParsedText']) ? $data['ParsedResults'][0]['ParsedText'] : null;
    }

    function verifyTesda () {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        }
    }

    function tesdaAPI ($firstName, $lastName, $certFirst4, $certLast4) {
        $apiUrl = 'https://scrapetesda.onrender.com/scrape?' . http_build_query([
            'fname' => $firstName,
            'lname' => $lastName,
            'c1'    => $certFirst4,
            'c2'    => $certLast4
        ]);

        $response = file_get_contents($apiUrl);
        if ($response === false) {
            return [
                'success' => false,
                'error' => 'Unable to connect to TESDA verification API'
            ];
        }

        $result = json_decode($response, true);

        if (!isset($result['success']) || !$result['success']) {
            return [
                'success' => false,
                'error' => 'TESDA API returned an error or invalid response'
            ];
        }

        if (empty($result['data'])) {
            return [
                'success' => false,
                'error' => 'No matching TESDA record found'
            ];
        }

        return [
            'success' => true,
            'data' => $result['data']
        ];
    }

}
?>