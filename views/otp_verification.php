<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $base_url = dirname($_SERVER['SCRIPT_NAME'], 2) . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/otp_verification.css">
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="otp-card w-full max-w-md p-8 animate-fade-in">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Verify OTP</h1>
            <p class="text-gray-500">Check your email for the code we sent you.</p>
        </div>

        <div class="flex flex-col items-center justify-center mb-6">
            <p id="timer" class="text-gray-600 mb-2">Time remaining: 2:00</p>
            <button id="resendOtpBtn"
                    class="text-gray-400 cursor-not-allowed flex items-center gap-2 px-4 py-2  rounded-md text-sm font-medium transition disabled:cursor-not-allowed disabled:text-gray-400"
                    disabled>
                <span id="resendText">Resend</span>
                <span id="resendSpinner" class="hidden"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
        </div>


        <form id="otp-form" class="space-y-6">
            <div class="relative">
                <input id="otpInput" type="text" name="otp"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"
                       placeholder="Enter OTP" required>
            </div>

            <button id="verifyOtpBtn" type="submit"
                    class="btn-verify w-full py-3 rounded-lg text-white font-semibold relative">
                <span>Verify OTP</span>
            </button>
        </form>
    </div>
    <script>
        var user_id = '<?php echo $_SESSION['user_id'] ?? ''; ?>';
        var user_id = '<?php echo $_SESSION['user_id'] ?? ''; ?>';
        var email = '<?php echo $_SESSION['user_email'] ?? ''; ?>';
        const BASE_URL = "<?= $base_url ?>";
    </script>
    <script src="script/otp_verification.js"></script>
</body>
</html>






