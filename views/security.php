<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/security.css">
</head>
<body>

    <!-- Password & Security Modal -->
    <div id="securityPopup" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden backdrop-blur-sm">
        <div class="bg-white p-6 rounded-xl security-card w-full max-w-md mx-4">
            <!-- Decorative header line -->
            <div class="header-line"></div>
            
            <div class="modal-content">
                <div class="flex justify-between items-center mb-6 pt-2">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-shield-alt mr-2 text-blue-500"></i>
                        Security Center
                    </h2>
                    
                    <!-- Close Button -->
                    <button onclick="closeSecurityPopup()" 
                        class="close-btn w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Security Options -->
                <div class="space-y-4">
                    <!-- Change Password Card -->
                    <div class="p-4 border border-gray-100 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                <i class="fas fa-key text-blue-600 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">Password</h3>
                                <p class="text-sm text-gray-500 mb-3">Update your account password regularly</p>
                                <button onclick="openChangePasswordPopup()" 
                                    class="btn-security btn-password text-white py-2 px-6 rounded-lg font-medium">
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 2FA Card -->
                    <div class="p-4 border border-gray-100 rounded-lg bg-gradient-to-r from-green-50 to-teal-50">
                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fas fa-mobile-alt text-green-600 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">Two-Factor Authentication</h3>
                                <p class="text-sm text-gray-500 mb-3">Add an extra layer of security to your account</p>
                                <button onclick="openTwoFactorAuthPopup()" 
                                    class="btn-security btn-2fa text-white py-2 px-6 rounded-lg font-medium">
                                    Set Up 2FA
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Security Tips -->
                    <div class="p-4 bg-yellow-50 border border-yellow-100 rounded-lg">
                        <div class="flex items-start">
                            <div class="bg-yellow-100 p-2 rounded-full mr-3">
                                <i class="fas fa-lightbulb text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700">
                                    <strong class="text-yellow-600">Tip:</strong> Always use a strong, unique password and enable 2FA for maximum security.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="script/security.js"></script>
    <?php include 'change_password.php'; ?>
</body>
</html>