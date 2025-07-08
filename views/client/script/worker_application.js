const uploadLabel = document.getElementById('uploadLabel');
const fileInput = document.getElementById('tesda_certificate');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
  uploadLabel.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
  e.preventDefault();
  e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
  uploadLabel.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
  uploadLabel.addEventListener(eventName, unhighlight, false);
});

function highlight() {
  uploadLabel.classList.add('upload-area-active');
}

function unhighlight() {
  uploadLabel.classList.remove('upload-area-active');
}

uploadLabel.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
  const dt = e.dataTransfer;
  const files = dt.files;
  fileInput.files = files;
  handleFiles(files);
}

function handleFiles(files) {
  if (files.length > 0) {
    document.getElementById('tesda_file_name').textContent = files[0].name;
  }
}

fileInput.addEventListener('change', function() {
  handleFiles(this.files);
});

function openVerifyWorkerModal() {
  document.getElementById("verifyWorkerModal").classList.remove("hidden");
  document.body.style.overflow = 'hidden';
}

function closeVerifyWorkerModal() {
  document.getElementById("verifyWorkerModal").classList.add("hidden");
  document.body.style.overflow = 'auto';
  document.getElementById("verifyWorkerForm").reset();
  document.getElementById("tesda_file_name").textContent = "";
  uploadLabel.classList.remove('upload-area-active');
}

document.getElementById("verifyWorkerForm").addEventListener("submit", async function(event) {
  event.preventDefault();
  
  const submitBtn = event.target.querySelector('button[type="submit"]');
  const submitText = document.getElementById('submitVerifyText');
  const submitSpinner = document.getElementById('submitVerifySpinner');
  
  submitText.textContent = 'Uploading...';
  submitSpinner.classList.remove('hidden');
  submitBtn.disabled = true;
  
  try {
    const formData = new FormData(this);
    const response = await fetch( BASE_URL + "controllers/routes.php?action=validateCertificate", {
      method: "POST",
      body: formData
    });
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    
    if (!data.success) {
      throw new Error(data.error || "Verification failed");
    }
    
    await Swal.fire({
      icon: 'success',
      title: 'Document Uploaded!',
      text: data.message,
      confirmButtonText: 'Continue',
      confirmButtonColor: '#4f46e5',
      customClass: {
        popup: 'rounded-xl',
        confirmButton: 'px-4 py-2 rounded-lg'
      }
    });
    
    closeVerifyWorkerModal();
    openExtraInfoModal(data.file_path);
  } catch (error) {
    console.error('Upload error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Upload Failed',
      text: error.message || 'Please try again later',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4f46e5',
      customClass: {
        popup: 'rounded-xl'
      }
    });
  } finally {
    submitText.textContent = 'Submit Verification';
    submitSpinner.classList.add('hidden');
    submitBtn.disabled = false;
  }
});

function openExtraInfoModal(filePath) {
  console.log("Setting file path:", filePath);
  const hiddenInput = document.getElementById("tesda_certificate_path");
  if (hiddenInput) {
    hiddenInput.value = filePath;
    console.log("Hidden input value set to:", hiddenInput.value);
    document.getElementById("extraInfoModal").classList.remove("hidden");
    document.body.style.overflow = 'hidden';
  } else {
    console.error("Could not find hidden input field");
  }
}

function closeExtraInfoModal() {
  document.getElementById("extraInfoModal").classList.add("hidden");
  document.body.style.overflow = 'auto';
  document.getElementById("extraInfoForm").reset();
}

document.getElementById("extraInfoForm").addEventListener("submit", function(event) {
  event.preventDefault();  

  const formData = new FormData(this);
  
  const submitBtn = event.target.querySelector('button[type="submit"]');
  const submitText = document.getElementById('submitInfoText');
  const submitSpinner = document.getElementById('submitInfoSpinner');
  
  submitText.textContent = 'Processing...';
  submitSpinner.classList.remove('hidden');
  submitBtn.disabled = true;

  Swal.fire({
    title: 'Verifying...',
    html: 'We are processing your verification details',
    timerProgressBar: true,
    didOpen: () => {
      Swal.showLoading();
    },
    allowOutsideClick: false,
    showConfirmButton: false,
    customClass: {
      popup: 'rounded-xl'
    }
  });
  
  fetch( BASE_URL + "controllers/routes.php?action=verifyTesda", {
    method: "POST",
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    Swal.close();
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Verification Complete!',
        text: data.message,
        confirmButtonText: 'Done',
        confirmButtonColor: '#4f46e5',
        customClass: {
          popup: 'rounded-xl',
          confirmButton: 'px-4 py-2 rounded-lg'
        }
      }).then(() => {
        closeExtraInfoModal();
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Verification Failed',
        text: data.error || "Something went wrong. Please try again.",
        confirmButtonText: 'OK',
        confirmButtonColor: '#4f46e5',
        customClass: {
          popup: 'rounded-xl'
        }
      });
    }
  })
  .catch(() => {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: "An error occurred while processing your request. Please try again.",
      confirmButtonText: 'OK',
      confirmButtonColor: '#4f46e5',
      customClass: {
        popup: 'rounded-xl'
      }
    });
  })
  .finally(() => {
    submitText.textContent = 'Complete Verification';
    submitSpinner.classList.add('hidden');
    submitBtn.disabled = false;
  });
});

// Close modals when clicking outside
document.addEventListener('click', function(event) {
  if (event.target.id === 'verifyWorkerModal') {
    closeVerifyWorkerModal();
  }
  if (event.target.id === 'extraInfoModal') {
    closeExtraInfoModal();
  }
});
