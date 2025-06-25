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

    fetch('http://localhost/handylink/controllers/routes.php?action=login', {
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