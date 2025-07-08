// Toggle password visibility
function togglePasswordVisibility(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    }
}

// Check password strength
document.getElementById('newPassword').addEventListener('input', function() {
    const password = this.value;
    const strengthText = document.getElementById('passwordStrength');
    const tipsText = document.getElementById('passwordTips');

    if (password.length === 0) {
        strengthText.textContent = '';
        tipsText.classList.remove('hidden');
        return;
    }

    tipsText.classList.add('hidden');

    // Simple strength check
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^A-Za-z0-9]/)) strength++;

    let strengthMessage = '';
    let color = '';

    switch (strength) {
        case 0:
        case 1:
            strengthMessage = 'Weak';
            color = 'text-red-500';
            break;
        case 2:
            strengthMessage = 'Fair';
            color = 'text-yellow-500';
            break;
        case 3:
            strengthMessage = 'Good';
            color = 'text-blue-500';
            break;
        case 4:
            strengthMessage = 'Strong';
            color = 'text-green-500';
            break;
    }

    strengthText.textContent = `Strength: ${strengthMessage}`;
    strengthText.className = `font-medium ${color}`;
});

// Check password match
document.getElementById('confirmPassword').addEventListener('input', function() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = this.value;
    const matchIndicator = document.getElementById('passwordMatch');

    if (confirmPassword.length === 0) {
        matchIndicator.classList.add('hidden');
        return;
    }

    if (newPassword === confirmPassword && newPassword.length > 0) {
        matchIndicator.classList.remove('hidden');
    } else {
        matchIndicator.classList.add('hidden');
    }
});

function openChangePasswordPopup() {
    closeSecurityPopup();
    const popup = document.getElementById('changePasswordPopup');
    if (popup) {
        popup.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeChangePasswordPopup() {
    const popup = document.getElementById('changePasswordPopup');
    if (popup) {
        popup.classList.add('hidden');
        document.body.style.overflow = 'auto';
        // Reset form
        document.getElementById('changePasswordForm').reset();
        document.getElementById('passwordMatch').classList.add('hidden');
        document.getElementById('passwordStrength').textContent = '';
        document.getElementById('passwordTips').classList.remove('hidden');
    }
}

// Close when clicking outside
document.addEventListener('click', (e) => {
    const popup = document.getElementById('changePasswordPopup');
    if (e.target === popup) {
        closeChangePasswordPopup();
    }
});

document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
    event.preventDefault();

    // Show loading state
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    const submitBtn = event.target.querySelector('button[type="submit"]');

    submitText.textContent = 'Updating...';
    submitSpinner.classList.remove('hidden');
    submitBtn.disabled = true;

    const formData = new FormData(this);

    // Check if passwords match
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword !== confirmPassword) {
        Swal.fire({
            title: 'Error!',
            text: 'Passwords do not match. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });

        submitText.textContent = 'Update Password';
        submitSpinner.classList.add('hidden');
        submitBtn.disabled = false;
        return;
    }

    // Simple password strength validation
    if (newPassword.length < 8) {
        Swal.fire({
            title: 'Weak Password',
            text: 'Your password should be at least 8 characters long.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });

        submitText.textContent = 'Update Password';
        submitSpinner.classList.add('hidden');
        submitBtn.disabled = false;
        return;
    }

    fetch( BASE_URL + "controllers/routes.php?action=changePass", {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg'
                }
            }).then(() => {
                closeChangePasswordPopup();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while processing your request.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        submitText.textContent = 'Update Password';
        submitSpinner.classList.add('hidden');
        submitBtn.disabled = false;
    });
});
