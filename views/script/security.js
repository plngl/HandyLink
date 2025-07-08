// Open Password & Security Modal
function openSecurityPopup() {
    const popup = document.getElementById('securityPopup');
    if (popup) {
        popup.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

// Close Password & Security Modal
function closeSecurityPopup() {
    const popup = document.getElementById('securityPopup');
    if (popup) {
        popup.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Close when clicking outside
document.addEventListener('click', (e) => {
    const popup = document.getElementById('securityPopup');
    if (e.target === popup) {
        closeSecurityPopup();
    }
});

// Placeholder functions - implement these as needed
function openChangePasswordPopup() {
    console.log('Opening change password popup');
    // Implement your change password popup logic here
}

function openTwoFactorAuthPopup() {
    console.log('Opening 2FA setup popup');
    // Implement your 2FA setup popup logic here
}
