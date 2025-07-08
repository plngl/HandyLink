<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add New Service</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
      font-family: 'Inter', sans-serif;
    }

    .modal-card {
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
      border-radius: 14px;
      position: relative;
      overflow: hidden;
      max-height: 90vh;
      overflow-y: auto;
    }

    .header-accent {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: #4f46e5;
    }

    .btn-primary {
      background: #4f46e5;
      transition: all 0.2s ease;
    }

    .btn-primary:hover {
      background: #4338ca;
      transform: translateY(-1px);
    }

    .close-btn {
      transition: all 0.2s ease;
    }

    .close-btn:hover {
      transform: rotate(90deg);
      color: #ef4444;
    }

    .popup-animate {
      animation: fadeInUp 0.25s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    input:focus, textarea:focus, select:focus {
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
      border-color: #4f46e5;
    }

    .upload-area {
      border: 2px dashed rgba(79, 70, 229, 0.4);
      transition: all 0.2s ease;
      padding: 1.75rem;
    }

    .upload-area:hover {
      background-color: rgba(79, 70, 229, 0.05);
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
  <!-- Modal -->
  <div id="popupModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white modal-card w-full max-w-lg mx-4 relative popup-animate">
      <div class="header-accent"></div>

      <div class="p-6">
        <button onclick="toggleModal(false)" class="close-btn absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500">
          <i class="fas fa-times"></i>
        </button>

        <div class="mb-5">
          <h2 class="text-xl font-bold text-gray-800">
            <i class="fas fa-plus-circle mr-2 text-indigo-500"></i>
            Add New Service
          </h2>
          <p class="text-gray-500 text-sm mt-1">Fill in the details to create your service offering</p>
        </div>

        <!-- Form -->
        <form id="serviceForm" enctype="multipart/form-data" class="space-y-5">
          <input type="hidden" name="user_id" value="">

          <!-- Image Upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Service Image</label>
            <label class="upload-area rounded-xl cursor-pointer block hover:border-indigo-500">
              <div class="flex flex-col items-center justify-center text-center" id="uploadText">
                <i class="fas fa-cloud-upload-alt text-3xl text-indigo-400 mb-3"></i>
                <p class="text-sm font-medium text-gray-600">Click to upload or drag & drop</p>
                <p class="text-xs text-gray-400 mt-1">JPG, PNG (Max 5MB)</p>
              </div>
              <input type="file" name="image" class="hidden" accept="image/*" id="imageInput">
            </label>
            <div class="text-xs text-gray-600 mt-2" id="fileNameDisplay"></div>
          </div>

          <!-- Name, Category, Subcategory -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Service Name</label>
              <input type="text" name="name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none" placeholder="e.g. Plumbing Repair" required>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
              <select name="category" id="categoryDropdown" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none" required>
                <option value="">Loading categories...</option>
              </select>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Subcategory</label>
              <select name="subcategory" id="subcategoryDropdown" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none" required>
                <option value="">Select a subcategory</option>
              </select>
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg resize-none focus:outline-none" placeholder="Describe your service in detail..." required></textarea>
          </div>

          <!-- Price -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Price (₱)</label>
              <input type="number" name="min_price" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none" required>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Price (₱)</label>
              <input type="number" name="max_price" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none" required>
            </div>
          </div>

          <!-- Submit -->
          <div class="pt-4">
            <button type="submit" class="btn-primary w-full py-3 rounded-lg text-white font-medium flex items-center justify-center gap-2 hover:shadow-md">
              <i class="fas fa-check-circle"></i> Create Service
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function toggleModal(show) {
      const modal = document.getElementById('popupModal');
      modal.classList.toggle('hidden', !show);
      document.body.style.overflow = show ? 'hidden' : 'auto';
      if (show) fetchCategories();
    }

    function fetchCategories() {
      fetch(BASE_URL + 'controllers/routes.php?action=getCategories')
        .then(res => res.json())
        .then(data => {
          const categoryDropdown = document.getElementById('categoryDropdown');
          categoryDropdown.innerHTML = '<option value="">Select a category</option>';
          data.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat.id;
            opt.textContent = cat.name;
            categoryDropdown.appendChild(opt);
          });
        });
    }

    document.getElementById('categoryDropdown').addEventListener('change', function () {
      const categoryId = this.value;
      const subDropdown = document.getElementById('subcategoryDropdown');
      subDropdown.innerHTML = '<option>Loading subcategories...</option>';

      fetch(BASE_URL + `controllers/routes.php?action=getSubcategories&category_id=${categoryId}`)
        .then(res => res.json())
        .then(data => {
          subDropdown.innerHTML = '<option value="">Select a subcategory</option>';
          data.forEach(sub => {
            const opt = document.createElement('option');
            opt.value = sub.id;
            opt.textContent = sub.name;
            subDropdown.appendChild(opt);
          });
        });
    });

    document.getElementById('imageInput').addEventListener('change', function () {
      const fileDisplay = document.getElementById('fileNameDisplay');
      if (this.files.length > 0) {
        fileDisplay.textContent = "Selected: " + this.files[0].name;
      } else {
        fileDisplay.textContent = '';
      }
    });

    document.getElementById('serviceForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);
      const submitBtn = form.querySelector('button[type="submit"]');

      submitBtn.disabled = true;
      submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Uploading...`;

      fetch(BASE_URL + 'controllers/routes.php?action=submitService', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(result => {
          alert(result.message || "Service created successfully!");
          toggleModal(false);
          form.reset();
          document.getElementById('fileNameDisplay').textContent = '';
        })
        .catch(err => {
          console.error(err);
          alert("An error occurred. Please try again.");
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = `<i class="fas fa-check-circle"></i> Create Service`;
        });
    });

    // Close modal when clicking outside
    document.addEventListener('click', function (e) {
      if (e.target.id === 'popupModal') {
        toggleModal(false);
      }
    });
  </script>
</body>
</html>
