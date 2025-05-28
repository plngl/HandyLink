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

</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
        <h2 class="text-2xl font-semibold mb-6 text-center text-indigo-600">Verify OTP</h2>

        <!-- Informative message and resend button -->
        <p class="text-center text-gray-600 mb-4">Please check your email for verification. Didn't get the code? 
            <button id="resendOtpBtn" class="text-gray-500 cursor-not-allowed" disabled>Resend</button>
        </p>

        <!-- Timer -->
        <p id="timer" class="text-center text-gray-600 mb-4">Time remaining: 2:00</p>

        <form id="otp-form" action="../controllers/OtpController.php" method="POST">
            <input type="hidden" name="activation_code" value="">

            <div class="input-container mb-4">
                <input id="otpInput" type="text" name="otp" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter OTP" required>
            </div>

            <button id="verifyOtpBtn" type="submit" class="w-full py-2 bg-indigo-600 text-white font-semibold rounded-lg">Verify OTP</button>
        </form>

    </div>

    <script>
        // Timer logic for 2 minutes countdown
        let timer;
        let minutes = 2; // 2 minutes
        let seconds = 0;

        function startTimer() {
            timer = setInterval(function() {
                if (seconds === 0) {
                    if (minutes === 0) {
                        clearInterval(timer);
                        // Enable the resend button and change color to indigo
                        const resendBtn = document.getElementById('resendOtpBtn');
                        resendBtn.disabled = false; // Remove the disabled attribute
                        resendBtn.classList.remove('text-gray-500', 'cursor-not-allowed'); // Remove grey and disabled styles
                        resendBtn.classList.add('text-indigo-600'); // Add indigo color
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

        // Verify OTP
        document.getElementById("verifyOtpBtn").addEventListener("click", function (event) {
            event.preventDefault(); // Prevent form submission

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
                console.log("Response received:", data); 

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'login.php'; 
                    });
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            })
            .catch(error => {
                console.error("Fetch error:", error);
            });
        });

        // Resend OTP
        document.getElementById('resendOtpBtn').addEventListener('click', function () {
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
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            });
        });
    </script>

</body>
</html>






