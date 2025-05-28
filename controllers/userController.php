<?php 
require_once '../config/Database.php';
require_once '../models/userModel.php';

class UserController {
    private $conn;
    private $userModel;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        $this->userModel = new UserModel($this->conn);
    }

    public function registration () {
        if($_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $user_name = $_POST['fullname'] ?? '';
            $user_email = $_POST['email'] ?? '';
            $user_password = $_POST['password'] ?? '';
            
            if($this->userModel->emailExists($user_email)){
                echo json_encode(["success" => false, "error" => "Email is already registered!"]);
                exit;
            }

            $result = $this->userModel->registerUser($user_name, $user_email, $user_password);
            
            if ($result) {
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['user_email'] = $user['Email'];
                
                echo json_encode(["success" => true, "message" => "Registration successful! You can now log in."]);
            } else {
                echo json_encode(["success" => false, "error" => "Something went wrong, please try again!"]);
            }
            exit;
        }
    }
}
?>