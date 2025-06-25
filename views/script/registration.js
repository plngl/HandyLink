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

// Toggle confirm password visibility
document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
    const confirmPasswordInput = document.getElementById('confirm_password');
    const icon = this;
            
    if (confirmPasswordInput.type === 'password') {
        confirmPasswordInput.type = 'text';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    } else {
        confirmPasswordInput.type = 'password';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    }
});

        // Form validation
$(document).ready(function() {
    $("#registerForm").on("submit", function(e) {
        e.preventDefault();
                
        const registerButton = document.getElementById('registerButton');
        const registerText = document.getElementById('registerText');
        const registerSpinner = document.getElementById('registerSpinner');
                    
        registerText.textContent = 'Registering...';
        registerSpinner.classList.remove('hidden');
        registerButton.disabled = true;

        let fullname = $("#fullname").val().trim();
        let email = $("#email").val().trim();
        let password = $("#password").val().trim();
        let confirmPassword = $("#confirm_password").val().trim();
        let isValid = true;

        // Full name validation
        if (fullname.length < 3) {
            isValid = false;
            Swal.fire({
                icon: 'error',
                title: 'Invalid Name',
                text: 'Full name must be at least 3 characters',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6366f1'
                });
        }

        // Email validation
        let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(email)) {
            isValid = false;
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6366f1'
                });
        }

        // Password validation
        let passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;
            if (!passwordPattern.test(password)) {
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must be at least 8 characters with uppercase, lowercase, and a number',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6366f1'
                });
            }

        // Confirm password validation
        if (password !== confirmPassword) {
            isValid = false;
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'Passwords do not match',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6366f1'
            });
        }

        if (!isValid) {
            registerText.textContent = 'Register';
            registerSpinner.classList.add('hidden');
            registerButton.disabled = false;
            return false;
        }

        // If validation passes, submit the form
        let formData = new FormData(this);

        $.ajax({
            url: BASE_URL + "controllers/routes.php?action=registration",
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: data.message,
                        confirmButtonText: 'Continue',
                        confirmButtonColor: '#6366f1',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = 'otp_verification.php?code=' + data.activation_code;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: data.error || 'An error occurred during registration',
                        confirmButtonText: 'Try Again',
                        confirmButtonColor: '#6366f1'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'An unexpected error occurred',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6366f1'
                });
            },
            complete: function() {
                registerText.textContent = 'Register';
                registerSpinner.classList.add('hidden');
                registerButton.disabled = false;
            }
        });

    });
});