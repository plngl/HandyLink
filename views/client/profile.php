<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $name = $_SESSION['name'] ?? '';
    $user_email = $_SESSION['user_email'] ?? '';
    $profile = $_SESSION['profile'] ?? '';

    $base_url = dirname($_SERVER['SCRIPT_NAME'], 2) . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../views/client/css/profile.css">
</head>
<body class="bg-gray-50">

    <!-- Popup Container -->
    <div id="accountPopup" class="fixed inset-0 flex items-center justify-center bg-black/50 hidden px-4 sm:px-0 z-50 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md flex flex-col justify-between relative popup-animate overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
            
            <button onclick="closeAccountPopup()" 
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-100 rounded-full hover:bg-gray-200 transition-all duration-200 close-btn text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Account Information</h2>
                <p class="text-gray-500 mt-1">Update your profile details</p>
            </div>
            
            <!-- Profile Picture Section -->
            <div class="flex flex-col items-center mb-6 relative">
                <label for="profilePic" class="cursor-pointer relative group">
                    <div class="w-28 h-28 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden shadow-md border-4 border-white profile-ring relative">
                        <img id="profileImage" 
                            src="<?php echo !empty($profile) ? '../images/profile/' . htmlspecialchars($profile, ENT_QUOTES, 'UTF-8') : '../images/profile/default.jpg'; ?>" 
                            alt="Profile" 
                            class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
            
                    <div class="absolute bottom-1 right-1 bg-indigo-600 p-2 rounded-full border-2 border-white group-hover:bg-indigo-700 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </label>
            </div>
            
            <form id="userDetails" class="flex flex-col flex-grow space-y-4">
                <input type="file" id="profilePic" name="profile_picture" accept="image/*" class="hidden" onchange="previewImage(event)">
                
                <!-- Name Field (replaced first and last name) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-0 input-focus transition-all duration-200 text-gray-700 placeholder-gray-400">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email, ENT_QUOTES, 'UTF-8'); ?>" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed" readonly>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 pb-2">
                    <button type="button" onclick="submitForm()" 
                        class="w-full px-6 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition-colors shadow-md">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const BASE_URL = "<?= $base_url ?>";
    </script>
    <script src="../views/client/script/profile.js"></script>
</body>
</html>







