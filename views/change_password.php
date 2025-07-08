<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style/change_pass.css">
</head>
<body>

    <!-- Change Password Modal -->
    <div id="changePasswordPopup" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden backdrop-blur-sm">
        <div class="bg-white p-6 rounded-xl password-card w-full max-w-md mx-4">
            <!-- Decorative header line -->
            <div class="header-accent"></div>
            
            <div class="modal-content">
                <div class="flex justify-between items-center mb-6 pt-2">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-key mr-2 text-blue-500"></i>
                        Change Password
                    </h2>
                    
                    <!-- Close Button -->
                    <button onclick="closeChangePasswordPopup()" 
                        class="close-btn w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Change Password Form -->
                <form id="changePasswordForm" action="#" method="POST" class="space-y-5">
                    <!-- Current Password -->
                    <div class="relative">
                        <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="currentPassword" name="current_password" 
                            class="input-password w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-0" 
                            placeholder="Enter your current password" required>
                        <i class="fas fa-eye-slash password-toggle absolute right-3" 
                            onclick="togglePasswordVisibility('currentPassword', this)"></i>
                    </div>
                    
                    <!-- New Password -->
                    <div class="relative">
                        <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" id="newPassword" name="new_password" 
                            class="input-password w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-0" 
                            placeholder="Create a new password" required>
                        <i class="fas fa-eye-slash password-toggle absolute right-3" 
                            onclick="togglePasswordVisibility('newPassword', this)"></i>
                        <div class="text-xs text-gray-500 mt-1">
                            <span id="passwordStrength" class="font-medium"></span>
                            <span id="passwordTips">Use 8+ characters with a mix of letters, numbers & symbols</span>
                        </div>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="relative">
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirm_password" 
                            class="input-password w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-0" 
                            placeholder="Re-enter your new password" required>
                        <i class="fas fa-eye-slash password-toggle absolute right-3" 
                            onclick="togglePasswordVisibility('confirmPassword', this)"></i>
                        <div id="passwordMatch" class="text-xs mt-1 hidden">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            <span class="text-green-600">Passwords match!</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit w-full py-3 rounded-lg text-white font-medium mt-2 flex items-center justify-center">
                        <span id="submitText">Update Password</span>
                        <span id="submitSpinner" class="hidden ml-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="script/change_pass.js"></script>

</body>
</html>