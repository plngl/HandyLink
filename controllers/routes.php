<?php
require_once 'userController.php';
require_once 'otpController.php';
require_once 'serviceController.php';

$userController = new UserController();
$otpController = new OtpController();
$serviceContoller = new serviceController(); 

$action = $_GET['action'] ?? '';

if (!isset($_GET['action'])) {
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

switch($action) {
    case 'registration':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->registration();
        } else {
            echo json_encode(["error" => "Invalid request method"]);
        }
        break;

    case 'otp_verification':
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (isset($_POST['action'])) {
                $action = $_POST['action'];
        
                if ($action == 'verify_otp') {
                    $otpController->verify();
                } elseif ($action == 'resend_otp') {
                    $otpController->resendOtp();
                } else {
                    echo json_encode(['success' => false, 'error' => 'Invalid action.']);
                }
            }
        }
        break;
    
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->loginUser();
        } else {
            echo json_encode(["error" => "Invalid request method"]);
        }
        break;

    case 'home':
        if ($_SERVER['REQUEST_METHOD']  === 'GET') {
            $serviceContoller->displayService();
        }
        break;

    case 'updateProfile':
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->updateProfile();
        }
        break;

    default:
        echo json_encode(["error" => "Unknown action"]);
        break;
}
?>