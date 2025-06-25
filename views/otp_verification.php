<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .otp-card {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .btn-verify {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-verify:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            transform: translateY(-2px);
        }

        .btn-verify:active {
            transform: translateY(0);
        }

        .btn-verify::after {
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

        .btn-verify:hover::after {
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
        let timer;
        let minutes = 1;
        let seconds = 0;

        function startTimer() {
            timer = setInterval(function () {
                if (seconds === 0) {
                    if (minutes === 0) {
                        clearInterval(timer);
                        const resendBtn = document.getElementById('resendOtpBtn');
                        resendBtn.disabled = false;
                        resendBtn.classList.remove('text-gray-400', 'cursor-not-allowed');
                        resendBtn.classList.add('text-indigo-600');
                        document.getElementById('timer').innerHTML = "You can now resend the OTP.";
                    } else {
                        minutes--;
                        seconds = 59;
                    }
                } else {
                    seconds--;
                }

                document.getElementById('timer').innerHTML = `Time remaining: ${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
            }, 1000);
        }

        window.onload = startTimer;

        document.getElementById("verifyOtpBtn").addEventListener("click", function (event) {
            event.preventDefault();

            var user_id = '<?php echo $_SESSION['user_id'] ?? ''; ?>';
            var otp = document.getElementById('otpInput').value;

            fetch("http://localhost/handylink/controllers/routes.php?action=otp_verification", {
                method: 'POST',
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    'action': 'verify_otp',
                    'user_id': user_id,
                    'otp': otp
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Verified!',
                        text: data.message,
                        confirmButtonColor: '#6366f1'
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid OTP',
                        text: data.error,
                        confirmButtonColor: '#6366f1'
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Something went wrong. Try again.', 'error');
                console.error(error);
            });
        });

        document.getElementById('resendOtpBtn').addEventListener('click', function () {
            const resendBtn = this;
            const resendText = document.getElementById('resendText');
            const resendSpinner = document.getElementById('resendSpinner');

            // Show loading spinner
            resendText.textContent = "Sending...";
            resendSpinner.classList.remove('hidden');
            resendBtn.disabled = true;
            resendBtn.classList.add('cursor-not-allowed', 'text-gray-400');

            var user_id = '<?php echo $_SESSION['user_id'] ?? ''; ?>';
            var email = '<?php echo $_SESSION['user_email'] ?? ''; ?>';

            fetch("http://localhost/handylink/controllers/routes.php?action=otp_verification", {
                method: 'POST',
                body: new URLSearchParams({
                    'action': 'resend_otp',
                    'user_id': user_id,
                    'email': email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success', data.message, 'success');
                    // Reset timer
                    minutes = 2;
                    seconds = 0;
                    startTimer();
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Something went wrong.', 'error');
            })
            .finally(() => {
                // Restore button after 2s delay to simulate process
                setTimeout(() => {
                    resendText.textContent = "Resend";
                    resendSpinner.classList.add('hidden');
                    resendBtn.disabled = true;
                    resendBtn.classList.remove('text-indigo-600');
                    resendBtn.classList.add('text-gray-400');
                }, 2000);
            });
        });

    </script>
</body>
</html>






