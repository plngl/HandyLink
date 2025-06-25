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

        document.getElementById('timer').innerHTML =
            `Time remaining: ${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
    }, 1000);
}

window.onload = startTimer;



document.getElementById("verifyOtpBtn").addEventListener("click", function (event) {
    event.preventDefault();

    
    var otp = document.getElementById('otpInput').value;

    fetch(BASE_URL + "controllers/routes.php?action=otp_verification", {
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

    

    fetch(BASE_URL + "controllers/routes.php?action=otp_verification", {
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
