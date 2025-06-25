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

    public function loginUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $this->userModel->getUserEmail($email);
            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['profile'] = $user['profile'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['password'] = $user['password'];
                    $_SESSION['role'] = $user['role'];

                    $isOnline = $this->userModel->isOnline($_SESSION['user_email']);

                    if ($isOnline) {
                        echo json_encode([
                            "success" => true,
                            "message" => "Login successful!"
                        ]);
                    }
                    
                } else {
                    echo json_encode([
                        "success" => false,
                        "error" => "Incorrect password. Please try again."
                    ]);
                }
            } else {
                echo json_encode([
                    "success" => false,
                    "error" => "No user found with that email."
                ]);
            }
        }
    }
}
?>