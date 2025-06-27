<?php
require_once '../config/Database.php';
require_once '../models/otpModel.php';
require_once '../models/userModel.php';

class OtpController{
    private $conn;
    private $otpModel;
    private $userModel;
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
        $this->otpModel = new OtpModel($this->conn);
        $this->userModel = new userModel($this->conn);
    }

    public function verify() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_id = $_POST['user_id'];
            $otp = $_POST['otp'];

            $verificationResult = $this->otpModel->verifyOtp($user_id, $otp);

            $updateRole = $this->userModel->updateRole($user_id);

            if ($verificationResult['success'] && $updateRole) {
                echo json_encode(["success" => true, "message" => $verificationResult['message']]);
            } else {
                echo json_encode(["success" => false, "error" => $verificationResult['error']]);
            }
        }
    }

    public function resendOtp() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_id = $_POST['user_id'];

            $email = $this->userModel->getEmail($user_id);

            $resendResult = $this->otpModel->resendOtp($user_id, $email);
            
            echo json_encode($resendResult);
        }
    }
}
?>