<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $base_url = dirname($_SERVER['SCRIPT_NAME'], 2) . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <link rel="stylesheet" href="../views/client/css/user_verification.css">
</head>
<body class="bg-gray-100 overflow-hidden">

    <!-- ID Verification Popup -->
    <div id="verificationPopup" class="popup-overlay">
        <div class="popup-container mx-4">
            <div class="popup-header">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <i class="fas fa-id-card text-xl mr-3"></i>
                        <div>
                            <h2 class="text-xl font-bold">Identity Verification</h2>
                            <p class="text-blue-100 text-sm mt-1">Complete these steps to verify your account</p>
                        </div>
                    </div>
                    <button onclick="closeVerificationPopup()" class="close-btn text-white text-xl w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form id="verificationForm" method="post" enctype="multipart/form-data" class="p-6 space-y-6">
                <!-- Step 1: Upload ID -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <div class="step-number">1</div>
                        <h3 class="font-semibold text-gray-800">Upload Your ID Document</h3>
                    </div>
                    
                    <div class="upload-area" id="uploadContainer">
                        <input type="file" name="id_photo" id="id_photo" accept="image/*" required 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <label for="id_photo" class="flex flex-col items-center justify-center p-6 cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-4xl text-blue-400 mb-3"></i>
                            <p class="text-sm font-medium text-gray-600">Click to upload or drag & drop</p>
                            <p class="text-xs text-gray-400 mt-1">Supported formats: JPG, PNG (Max 5MB)</p>
                            <p class="text-xs text-gray-400">Ensure all details are clearly visible</p>
                        </label>
                    </div>
                    
                    <div id="idPreviewContainer" class="hidden">
                        <div class="relative">
                            <img id="idPreview" class="w-full max-h-20 object-contain rounded-lg border border-gray-200">
                            <div class="preview-remove" onclick="clearPreview('idPreview')">
                                <i class="fas fa-times text-xs"></i>
                            </div>
                        </div>
                        <p id="idFileName" class="text-xs text-gray-500 mt-1 truncate"></p>
                    </div>
                </div>

                <!-- Step 2: Take Selfie -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <div class="step-number">2</div>
                        <h3 class="font-semibold text-gray-800">Take a Live Selfie</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="camera-container" id="cameraBox">
                            <video id="video" autoplay playsinline class="w-full h-full object-cover"></video>
                            <canvas id="canvas" class="hidden"></canvas>
                            <canvas id="faceDetectionCanvas" class="hidden"></canvas>
                            <div class="face-outline"></div>
                            <div id="captureIndicator" class="absolute top-4 left-0 right-0 text-center text-white font-medium hidden">
                                <span id="captureStepText" class="bg-blue-500 px-3 py-1 rounded-full"></span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 justify-center">
                            <button type="button" id="captureBtn"
                                    class="btn-danger px-4 py-2 text-white rounded-lg font-medium flex items-center gap-2">
                                <i class="fas fa-camera mr-1"></i> Start Verification
                            </button>
                            <button type="button" id="retakeBtn"
                                    class="btn-secondary px-4 py-2 text-gray-700 rounded-lg font-medium flex items-center gap-2 hidden">
                                <i class="fas fa-sync-alt mr-1"></i> Retake
                            </button>
                        </div>
                        
                        <div id="selfiePreview" class="hidden">
                            <div class="relative">
                                <img id="selfieImage" class="mx-auto max-h-64 rounded-lg border-2 border-gray-200">
                                <div class="preview-remove" onclick="clearPreview('selfieImage')">
                                    <i class="fas fa-times text-xs"></i>
                                </div>
                            </div>
                            <input type="hidden" id="selfie_data" name="selfie_data">
                        </div>
                        
                        <!-- Face verification result indicator -->
                        <div id="faceMatchResult" class="hidden text-center p-3 rounded-lg mt-2">
                            <div class="flex items-center justify-center gap-2">
                                <i id="faceMatchIcon" class="fas fa-question-circle"></i>
                                <p id="faceMatchText" class="text-sm font-medium"></p>
                            </div>
                            <p id="similarityScore" class="text-xs mt-1"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" id="submitBtn"
                            class="btn-primary w-full py-3 text-white rounded-lg font-medium flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-right"></i> Next Step
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Personal Information Popup -->
    <div id="personalInfoPopup" class="popup-overlay">
        <div class="popup-container mx-4">
            <div class="popup-header">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <i class="fas fa-user-edit text-xl mr-3"></i>
                        <div>
                            <h2 class="text-xl font-bold">Personal Information</h2>
                            <p class="text-blue-100 text-sm mt-1">Please complete your personal details</p>
                        </div>
                    </div>
                    <button onclick="closePersonalInfoPopup()" class="close-btn text-white text-xl w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form id="personalInfoForm" class="p-6">
                <div class="space-y-6">
                    <!-- ID Number -->
                    <div>
                        <label for="idNumber" class="block text-sm font-medium text-gray-700 mb-2">ID Number <span class="text-red-500">*</span></label>
                        <input type="text" id="idNumber" name="idNumber" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400"
                            placeholder="Enter your ID number">
                    </div>
                    
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                            <input type="text" id="firstName" name="firstName" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400"
                                placeholder="Enter first name">
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" id="lastName" name="lastName" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400"
                                placeholder="Enter last name">
                        </div>
                    </div>
                    
                    <!-- Middle Name and Suffix -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="middleName" class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                            <input type="text" id="middleName" name="middleName"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-400"
                                placeholder="Enter middle name">
                        </div>
                        <div>
                            <label for="suffix" class="block text-sm font-medium text-gray-700 mb-2">Suffix (if any)</label>
                            <select id="suffix" name="suffix"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWNoZXZyb24tZG93biI+PHBhdGggZD0ibTYgOSA2IDYgNi02Ii8+PC9zdmc+')] bg-no-repeat bg-[center_right_1rem]">
                                <option value="">None</option>
                                <option value="Jr">Jr</option>
                                <option value="Sr">Sr</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sex and Birthday -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="sex" class="block text-sm font-medium text-gray-700 mb-2">Sex <span class="text-red-500">*</span></label>
                            <select id="sex" name="sex" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWNoZXZyb24tZG93biI+PHBhdGggZD0ibTYgOSA2IDYgNi02Ii8+PC9zdmc+')] bg-no-repeat bg-[center_right_1rem]">
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label for="birthday" class="block text-sm font-medium text-gray-700 mb-2">Birthday <span class="text-red-500">*</span></label>
                            <input type="date" id="birthday" name="birthday" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                    </div>
                    
                    <!-- Hidden fields -->
                    <input type="hidden" name="id_path" id="id_path">
                    <input type="hidden" name="selfie_path" id="selfie_path">
                </div>
                
                <!-- Submit Button -->
                <div class="mt-8">
                    <button type="submit" id="submitPersonalInfo"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center gap-3 shadow-md hover:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span>Submit Information</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function getCurrentUserId() {
            return <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>;
        }
    </script>
    <script src="../views/client/script/user_verification.js"></script>
</body>
</html>

