<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['user_id'])) {
        header("Location: ../views/home.php");
        exit;
    }
    $base_url = dirname($_SERVER['SCRIPT_NAME'], 2) . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/login.css">
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="login-card w-full max-w-md p-8 animate-fade-in">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h1>
            <p class="text-gray-500">Log in to access your account</p>
        </div>

        <form id="login-form" method="POST" class="space-y-6">
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
                <div class="absolute right-3 top-3">
                    <i class="fas fa-eye-slash text-gray-400 cursor-pointer" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" name="submit" id="loginButton"
                class="btn-login w-full py-3 rounded-lg text-white font-semibold relative">
                <span id="loginText">Log In</span>
                <span id="loginSpinner" class="hidden ml-2">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
        </form>

        <div class="text-center text-sm text-gray-600 mt-6">
            Don't have an account? 
            <a href="registration.php" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                Sign up here
            </a>
        </div>
    </div>
    <script>
        const BASE_URL = "<?= $base_url ?>";
    </script>
    <script src="script/login.js"></script>
</body>
</html>
