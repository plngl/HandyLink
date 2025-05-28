<?php 
if(isset($_SESSION['ID'])) {
    header("Location: ../views/service.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Your Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .login-card {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .input-container {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-container input {
            padding-top: 1.75rem;
            padding-bottom: 0.75rem;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }
        
        .input-container input:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            border-color: #6366f1;
            background-color: white;
        }
        
        .input-container label {
            position: absolute;
            top: 0.75rem;
            left: 1rem;
            font-size: 1rem;
            color: #64748b;
            transition: all 0.3s ease;
            pointer-events: none;
            background: linear-gradient(to bottom, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 50%, rgba(255,255,255,0) 100%);
            padding: 0 0.5rem;
        }
        
        .input-container input:focus + label,
        .input-container input:not(:placeholder-shown) + label {
            top: 0.25rem;
            left: 0.75rem;
            font-size: 0.75rem;
            color: #6366f1;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(30deg);
            transition: all 0.3s;
        }
        
        .btn-login:hover::after {
            left: 100%;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
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
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        });

        // Login Form Submission
        document.getElementById('login-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const loginButton = document.getElementById('loginButton');
            const loginText = document.getElementById('loginText');
            const loginSpinner = document.getElementById('loginSpinner');
            
            loginText.textContent = 'Logging in...';
            loginSpinner.classList.remove('hidden');
            loginButton.disabled = true;

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            fetch('http://localhost/capstone/controllers/routes.php?action=loginUser', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    'email': email,
                    'password': password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome Back!',
                        text: data.message,
                        confirmButtonText: 'Continue',
                        confirmButtonColor: '#6366f1',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'px-4 py-2 rounded-lg'
                        },
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = 'service.php';  
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: data.error,
                        confirmButtonText: 'Try Again',
                        confirmButtonColor: '#6366f1',
                        customClass: {
                            popup: 'rounded-xl'
                        }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'An unexpected error occurred.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6366f1',
                    customClass: {
                        popup: 'rounded-xl'
                    }
                });
            })
            .finally(() => {
                loginText.textContent = 'Log In';
                loginSpinner.classList.add('hidden');
                loginButton.disabled = false;
            });
        });
    </script>
</body>
</html>
