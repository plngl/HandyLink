<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['ID'])) {
        header("Location: service.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Your Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="registration.css">
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="register-card w-full max-w-md p-8 animate-fade-in">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h1>
            <p class="text-gray-500">Join us to get started</p>
        </div>

        <form id="registerForm" method="POST" class="space-y-6">
            <div class="input-container">
                <input type="text" id="fullname" name="fullname"
                    class="w-full px-4 border border-gray-200 rounded-lg focus:outline-none focus:ring-0"
                    placeholder=" " required>
                <label for="fullname">
                    <i class="fas fa-user mr-2"></i>Full Name
                </label>
            </div>

            <div class="input-container">
                <input type="email" id="email" name="email"
                    class="w-full px-4 border border-gray-200 rounded-lg focus:outline-none focus:ring-0"
                    placeholder=" " required>
                <label for="email">
                    <i class="fas fa-envelope mr-2"></i>Email address
                </label>
            </div>

            <div class="input-container">
                <input type="password" id="password" name="password"
                    class="w-full px-4 border border-gray-200 rounded-lg focus:outline-none focus:ring-0"
                    placeholder=" " required>
                <label for="password">
                    <i class="fas fa-lock mr-2"></i>Password
                </label>
                <i class="fas fa-eye-slash password-toggle" id="togglePassword"></i>
            </div>

            <div class="input-container">
                <input type="password" id="confirm_password" name="confirm_password"
                    class="w-full px-4 border border-gray-200 rounded-lg focus:outline-none focus:ring-0"
                    placeholder=" " required>
                <label for="confirm_password">
                    <i class="fas fa-lock mr-2"></i>Confirm Password
                </label>
                <i class="fas fa-eye-slash password-toggle" id="toggleConfirmPassword"></i>
            </div>

            <button type="submit" id="registerButton"
                class="btn-register w-full py-3 rounded-lg text-white font-semibold relative">
                <span id="registerText">Register</span>
                <span id="registerSpinner" class="hidden ml-2">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
        </form>

        <div class="text-center text-sm text-gray-600 mt-6">
            Already have an account? 
            <a href="login.php" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                Log in here
            </a>
        </div>
    </div>
    <script src="registration.js"></script>
</body>
</html>