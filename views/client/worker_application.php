<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verify Documents</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../views/client/css/worker_application.css">
</head>
<body class="bg-gray-50">

  <!-- Main Upload Modal -->
  <div id="verifyWorkerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden backdrop-blur-sm">
    <div class="bg-white p-6 modal-card w-full max-w-lg mx-4">
      <!-- Decorative header line -->
      <div class="header-accent"></div>
      
      <div class="modal-content">
        <div class="flex justify-between items-center mb-6 pt-2">
          <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-id-card mr-2 text-primary"></i>
            Worker Verification
          </h2>
          <button onclick="closeVerifyWorkerModal()" class="close-btn w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500">
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>
        
        <p class="text-gray-500 text-sm mb-6 text-center">
          Upload your TESDA certificate to verify your worker status and access more job opportunities
        </p>
        
        <form id="verifyWorkerForm" enctype="multipart/form-data" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
              <i class="fas fa-certificate mr-2 text-primary"></i>
              TESDA Certificate
            </label>
            <label id="uploadLabel" class="flex flex-col w-full h-40 upload-area rounded-xl cursor-pointer items-center justify-center">
              <div class="flex flex-col items-center justify-center pt-5 pb-6 px-4 text-center">
                <svg class="w-12 h-12 mb-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <p class="text-sm text-gray-600 font-medium">Drag & drop your file here</p>
                <p class="text-xs text-gray-400 mt-1">or click to browse (PNG, JPG, JPEG)</p>
                <span id="tesda_file_name" class="text-xs text-primary font-medium mt-2 truncate max-w-xs"></span>
              </div>
              <input id="tesda_certificate" name="tesda_certificate" type="file" class="hidden" accept="image/png, image/jpeg, image/jpg" required />
            </label>
          </div>
          
          <div class="flex items-center text-sm text-gray-500">
            <i class="fas fa-info-circle mr-2 text-primary"></i>
            <span>Your document will be verified within 24-48 hours</span>
          </div>
          
          <button type="submit" class="btn-primary w-full py-3 rounded-xl text-white font-medium flex items-center justify-center">
            <span id="submitVerifyText">Submit Verification</span>
            <span id="submitVerifySpinner" class="hidden ml-2">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Additional Information Modal -->
  <div id="extraInfoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden backdrop-blur-sm">
    <div class="bg-white p-6 modal-card w-full max-w-md mx-4">
      <!-- Decorative header line -->
      <div class="header-accent"></div>
      
      <div class="modal-content">
        <div class="flex justify-between items-center mb-6 pt-2">
          <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user-check mr-2 text-primary"></i>
            Additional Information
          </h2>
          <button onclick="closeExtraInfoModal()" class="close-btn w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500">
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>
        
        <p class="text-gray-500 text-sm mb-6">
          Please complete your verification by providing these additional details
        </p>
        
        <form id="extraInfoForm" class="space-y-4">
          <input type="hidden" name="tesda_certificate_path" id="tesda_certificate_path"/>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
            <input type="text" name="firstname" placeholder="Enter your first name" required 
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-0" />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
            <input type="text" name="lastname" placeholder="Enter your last name" required 
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-0" />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">TESDA Certificate Number</label>
            <div class="flex space-x-2">
              <input type="text" name="tesda_first_four" maxlength="4" placeholder="XXXX" required 
                     class="certificate-input w-full px-4 py-3 border border-gray-200 rounded-lg text-center" />
              <span class="flex items-center text-gray-400">-</span>
              <input type="text" name="tesda_last_four" maxlength="4" placeholder="XXXX" required 
                     class="certificate-input w-full px-4 py-3 border border-gray-200 rounded-lg text-center" />
            </div>
            <p class="text-xs text-gray-400 mt-1">Enter the first and last 4 digits of your certificate number</p>
          </div>
          
          <button type="submit" class="btn-primary w-full py-3 rounded-xl text-white font-medium flex items-center justify-center">
            <span id="submitInfoText">Complete Verification</span>
            <span id="submitInfoSpinner" class="hidden ml-2">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
          </button>
        </form>
      </div>
    </div>
  </div>
  <script>
    
  </script>
  <script src="../views/client/script/worker_application.js"></script>

</body>
</html>