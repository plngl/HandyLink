<?php
require_once 'userController.php';
require_once 'otpController.php';
require_once 'serviceController.php';
require_once 'verificationController.php';
require_once 'locationController.php';
require_once 'categoryController.php';

$userController = new UserController();
$otpController = new OtpController();
$serviceContoller = new serviceController();
$verifyController = new verificationController();
$locationController = new locationController();
$catController = new categoryController();

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
    
    case 'checkId':
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $verifyController->checkID();
        }
        break;
    
    case 'saveId':
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $verifyController->saveId();
        } 
        break;

    case 'validateCertificate':
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $verifyController->validateTesdaCertificate();
            }
        break;
    
    case 'saveTesda':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        }
        break;
    
    case 'changePass':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->changePassword();
        }
        break;

    case 'getLocation':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $locationController->saveLocation();
        }
        break;
    
    case 'displayCategory':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $catController->displayCategory();
        }
        break;

    case 'displaySubCategory':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $catController->getSubcategories();
        }
        break;

    default:
        echo json_encode(["error" => "Unknown action"]);
        break;
}
?>