function submitForm() {
    const profilePicInput = document.getElementById('profilePic');
    const name = document.querySelector('[name="name"]').value;
    const email = document.querySelector('[name="email"]').value;

    // Simple validation
    if (!name) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please enter your name',
            showConfirmButton: true
        });
        return;
    }

    const formData = new FormData();
    if (profilePicInput.files[0]) {
        formData.append('profile_picture', profilePicInput.files[0]);
    }
    formData.append('name', name);
    formData.append('email', email);

    // Show loading state
    const submitBtn = document.querySelector('[onclick="submitForm()"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Saving...
    `;

    fetch(BASE_URL + 'controllers/routes.php?action=updateProfile', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false,
                background: '#f8fafc',
                backdrop: `
                    rgba(0,0,0,0.5)
                    url("/images/nyan-cat.gif")
                    left top
                    no-repeat
                `
            }).then(() => {
                closeAccountPopup();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || 'Something went wrong.',
                showConfirmButton: true
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Connection Error',
            text: 'Failed to connect to the server.',
            showConfirmButton: true
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Save Changes';
    });
}

function openAccountPopup() {
    document.getElementById("accountPopup").classList.remove("hidden");
    document.body.style.overflow = 'hidden';
}

function closeAccountPopup() {
    document.getElementById("accountPopup").classList.add("hidden");
    document.body.style.overflow = 'auto';
}

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const output = document.getElementById('profileImage');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

document.addEventListener("click", function (event) {
    const popup = document.getElementById("accountPopup");
    if (event.target === popup) {
        closeAccountPopup();
    }
});
