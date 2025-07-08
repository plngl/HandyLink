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

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $user_id = $_SESSION['user_id'] ?? '';

            $response = ["success" => true, "messages" => []];

            // Handle profile picture upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $targetDir = __DIR__ . '/../images/profile/';
                $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
                $fileName = $_FILES['profile_picture']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExtension, $allowedExts)) {
                    $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
                    $destPath = $targetDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        if ($this->userModel->updateProfile($user_id, $newFileName)) {
                            $_SESSION['profile_picture'] = $newFileName;
                            $response['messages'][] = "Profile picture updated.";
                        } else {
                            $response = ["success" => false, "error" => "Failed to update profile picture in database."];
                            echo json_encode($response);
                            return;
                        }
                    } else {
                        $response = ["success" => false, "error" => "Failed to move uploaded profile picture."];
                        echo json_encode($response);
                        return;
                    }
                } else {
                    $response = ["success" => false, "error" => "Invalid image type."];
                    echo json_encode($response);
                    return;
                }
            }

            // Update name if it has changed
            if (!empty($name) && $name !== $_SESSION['name']) {
                if ($this->userModel->updateName($user_id, $name)) {
                    $_SESSION['name'] = $name;
                    $response['messages'][] = "Name updated successfully.";
                } else {
                    $response = ["success" => false, "error" => "Failed to update name."];
                    echo json_encode($response);
                    return;
                }
            }

            echo json_encode($response);
        }
    }

    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $response = ["success" => false, "messages" => []];
    
            $current_pass = $_POST['current_password'] ?? '';
            $pass = $_POST['new_password'] ?? '';
            $confirm_pass = $_POST['confirm_password'] ?? '';
    
            $user_password = $_SESSION['password'];
            $user_id = $_SESSION['user_id'];
    
            if ($pass !== $confirm_pass) {
                $response = ["success" => false, "error" => "Passwords do not match"];
                echo json_encode($response);
                return;
            }
    
            if (!password_verify($current_pass, $user_password)) {
                $response = ["success" => false, "error" => "Current password is incorrect"];
                echo json_encode($response);
                return;
            }

            $result = $this->userModel->updatePassword($user_id, $pass);

            if ($result) {
                $response = ["success" => true, "message" => "Password changed successfully"];
            } else {
                $response = ["success" => false, "error" => "Failed to update the password"];
            }
    
            echo json_encode($response);
        }
    }
}
?>