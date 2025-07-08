<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(!isset($_SESSION['user_id'])) {
        header("Location: ../views/login.php");
        exit;
    }

    $user_name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Services | HandyLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap');
        
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #f59e0b;
            --dark: #1f2937;
            --light: #f9fafb;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1f2937;
        }
        
        .heading-font {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1);
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
        }
        
        .tab-active {
            position: relative;
            color: var(--primary);
            font-weight: 600;
        }
        
        .tab-active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary);
            border-radius: 3px 3px 0 0;
        }
        
        .price-tag {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .rating-badge {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--secondary);
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .scroll-container {
            scrollbar-width: thin;
            scrollbar-color: var(--primary-light) transparent;
        }
        
        .scroll-container::-webkit-scrollbar {
            height: 6px;
        }
        
        .scroll-container::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .scroll-container::-webkit-scrollbar-thumb {
            background-color: var(--primary-light);
            border-radius: 6px;
        }
        
        .nav-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        
        .service-image {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .card-hover:hover .service-image {
            transform: scale(1.03);
        }
    </style>
</head>
<body class="font-sans bg-gray-50">
    <!-- Header -->
    <header class="bg-white nav-shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="home.php" class="flex items-center">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-800 heading-font">HandyLink</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-indigo-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 0V9.349m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                        </svg>
                    </a>
                    
                    <a href="chat.php" class="relative p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-indigo-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">3</span>
                    </a>
                    
                    <button class="relative p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-indigo-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">5</span>
                    </button>
                    
                    <div class="relative">
                        <button id="profileDropdownTrigger" class="flex items-center space-x-2 focus:outline-none">
                            <img src="<?php echo !empty( $_SESSION['profile']) ? '../images/profile/' . htmlspecialchars($_SESSION['profile'], ENT_QUOTES, 'UTF-8') : '../images/profile/default.jpg'; ?>" alt="User Profile" class="w-9 h-9 rounded-full object-cover border-2 border-indigo-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div id="profileDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-1 z-50 hidden border border-gray-100 divide-y divide-gray-100">
                            <div class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-800">
                                    <?php echo $user_name; ?>
                                </p>
                                <?php if ($_SESSION['role'] == 'worker') : ?>
                                <p class="text-xs text-gray-500">Professional Account</p>
                                <?php endif;?>
                                <?php if ($_SESSION['role'] == 'client') : ?>
                                <p class="text-xs text-gray-500">Client Account</p>
                                <?php endif;?>
                            </div>

                            <a href="#" onclick="event.preventDefault(); openAccountPopup();" 
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 rounded-md">
                                <i class="fas fa-user mr-3 text-indigo-600"></i>
                                <span>Profile</span>
                            </a>

                            <?php if ($_SESSION['role'] == "worker"): ?>
                                <a href="dashboard.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors rounded-md">
                                    <i class="fas fa-tachometer-alt mr-3 text-indigo-600"></i> Dashboard
                                </a>
                            <?php endif; ?>

                            <?php if ($_SESSION['role'] == "client"): ?>
                                <a href="#" onclick="event.preventDefault(); openVerificationPopup();" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors rounded-md">
                                    <i class="fas fa-check-circle mr-3 text-indigo-600"></i> Verify your Account
                                </a>
                                <a href="#" onclick="event.preventDefault(); openVerifyWorkerModal();" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors rounded-md">
                                    <i class="fas fa-hammer mr-3 text-indigo-600"></i> Be a Worker
                                </a>
                            <?php endif; ?>

                            <a href="#" onclick="event.preventDefault(); openSecurityPopup();" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors rounded-md">
                                <i class="fas fa-lock mr-3 text-indigo-600"></i> Security
                            </a>

                            <a href="#" onclick="event.preventDefault();" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors rounded-md">
                                <i class="fas fa-plus-circle mr-3 text-indigo-600"></i> Add New Service
                            </a>

                            <a href="logout.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors rounded-md">
                                <i class="fas fa-sign-out-alt mr-3 text-indigo-600"></i> Logout
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- Hero Section -->
        <section class="mb-12">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-2xl p-8 text-white">
                <h1 class="text-3xl md:text-4xl font-bold mb-4 heading-font">Find the perfect professional for your needs</h1>
                <p class="text-lg mb-6 max-w-2xl">Browse thousands of skilled professionals ready to help with your home services and projects.</p>
                <div class="relative max-w-xl">
                    <input type="text" placeholder="What service are you looking for?" class="w-full py-3 px-4 pr-12 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300 text-gray-800">
                    <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-indigo-700 text-white p-2 rounded-lg hover:bg-indigo-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Category Tabs -->
        <section class="mb-10">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="flex items-center">
                    <button class="p-3 text-gray-500 hover:text-indigo-600 transition-colors hidden md:block" onclick="scrollTabs(-100)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex-1 overflow-x-auto scroll-container">
                        <div class="tab flex space-x-1 px-1 py-2">
                            <button class="tablinks px-5 py-3 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors tab-active" data-category="All">All Services</button>
                            <button class="tablinks px-5 py-3 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" data-category="Plumbing">Plumbing</button>
                            <button class="tablinks px-5 py-3 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" data-category="Electrical">Electrical</button>
                            <button class="tablinks px-5 py-3 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" data-category="Cleaning">Cleaning</button>
                            <button class="tablinks px-5 py-3 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" data-category="Appliance Repair">Appliance Repair</button>
                            <button class="tablinks px-5 py-3 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" data-category="Carpentry">Carpentry</button>
                            <button class="tablinks px-5 py-3 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" data-category="Painting">Painting</button>
                            <button class="tablinks px-5 py-3 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" data-category="Pest Control">Pest Control</button>
                            <button class="tablinks px-5 py-3 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" data-category="Landscaping">Landscaping</button>
                            <button class="tablinks px-5 py-3 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" data-category="HVAC">HVAC</button>
                        </div>
                    </div>
                    <button class="p-3 text-gray-500 hover:text-indigo-600 transition-colors hidden md:block" onclick="scrollTabs(100)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Services Grid -->
        <section>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold heading-font">Recommended Services</h2>
                <div class="flex space-x-2">
                    <button class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filters
                    </button>
                    <button class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Sort by: Recommended
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Service Card 1 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 card-hover">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Plumbing Service" class="w-full service-image">
                        <div class="absolute top-3 right-3">
                            <button class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-red-500 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="John D." class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">John D.</p>
                                    <p class="text-xs text-gray-500">Plumbing Expert</p>
                                </div>
                            </div>
                            <span class="price-tag">₱500+</span>
                        </div>
                        <a href="#" class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors block mb-3">
                            Professional Plumbing Repair and Installation
                        </a>
                        <div class="flex items-center justify-between">
                            <span class="rating-badge">
                                <i class="fas fa-star mr-1"></i> 4.9 (89)
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i> 5km away
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Service Card 2 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 card-hover">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1590959651373-a3db0f38a961?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Electrical Service" class="w-full service-image">
                        <div class="absolute top-3 right-3">
                            <button class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-red-500 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Ella R." class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">Ella R.</p>
                                    <p class="text-xs text-gray-500">Electrician</p>
                                </div>
                            </div>
                            <span class="price-tag">₱650+</span>
                        </div>
                        <a href="#" class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors block mb-3">
                            Complete Home Electrical Wiring Solutions
                        </a>
                        <div class="flex items-center justify-between">
                            <span class="rating-badge">
                                <i class="fas fa-star mr-1"></i> 4.8 (73)
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i> 3km away
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Service Card 3 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 card-hover">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1581578731548-c64695cc6952?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Cleaning Service" class="w-full service-image">
                        <div class="absolute top-3 right-3">
                            <button class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-red-500 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/women/63.jpg" alt="Maria S." class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">Maria S.</p>
                                    <p class="text-xs text-gray-500">Cleaning Professional</p>
                                </div>
                            </div>
                            <span class="price-tag">₱400+</span>
                        </div>
                        <a href="#" class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors block mb-3">
                            Deep Cleaning for Homes and Apartments
                        </a>
                        <div class="flex items-center justify-between">
                            <span class="rating-badge">
                                <i class="fas fa-star mr-1"></i> 4.7 (112)
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i> 7km away
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Service Card 4 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 card-hover">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1556909212-d5b604d0c90d?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Appliance Repair" class="w-full service-image">
                        <div class="absolute top-3 right-3">
                            <button class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-red-500 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Robert T." class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">Robert T.</p>
                                    <p class="text-xs text-gray-500">Appliance Technician</p>
                                </div>
                            </div>
                            <span class="price-tag">₱750+</span>
                        </div>
                        <a href="#" class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors block mb-3">
                            Refrigerator and Washing Machine Repair
                        </a>
                        <div class="flex items-center justify-between">
                            <span class="rating-badge">
                                <i class="fas fa-star mr-1"></i> 4.9 (64)
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i> 4km away
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Service Card 5 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 card-hover">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1600585152220-90363fe7e115?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Carpentry Service" class="w-full service-image">
                        <div class="absolute top-3 right-3">
                            <button class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-red-500 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Michael W." class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">Michael W.</p>
                                    <p class="text-xs text-gray-500">Master Carpenter</p>
                                </div>
                            </div>
                            <span class="price-tag">₱900+</span>
                        </div>
                        <a href="#" class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors block mb-3">
                            Custom Furniture and Woodworking
                        </a>
                        <div class="flex items-center justify-between">
                            <span class="rating-badge">
                                <i class="fas fa-star mr-1"></i> 5.0 (42)
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i> 6km away
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Service Card 6 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 card-hover">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1518391846015-55a9cc003b25?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Painting Service" class="w-full service-image">
                        <div class="absolute top-3 right-3">
                            <button class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-red-500 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/men/28.jpg" alt="David L." class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">David L.</p>
                                    <p class="text-xs text-gray-500">Painting Specialist</p>
                                </div>
                            </div>
                            <span class="price-tag">₱550+</span>
                        </div>
                        <a href="#" class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors block mb-3">
                            Interior and Exterior House Painting
                        </a>
                        <div class="flex items-center justify-between">
                            <span class="rating-badge">
                                <i class="fas fa-star mr-1"></i> 4.8 (57)
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i> 2km away
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Service Card 7 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 card-hover">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Pest Control" class="w-full service-image">
                        <div class="absolute top-3 right-3">
                            <button class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-red-500 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Sarah K." class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">Sarah K.</p>
                                    <p class="text-xs text-gray-500">Pest Control Expert</p>
                                </div>
                            </div>
                            <span class="price-tag">₱800+</span>
                        </div>
                        <a href="#" class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors block mb-3">
                            Comprehensive Pest Elimination Service
                        </a>
                        <div class="flex items-center justify-between">
                            <span class="rating-badge">
                                <i class="fas fa-star mr-1"></i> 4.9 (38)
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i> 8km away
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Service Card 8 -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 card-hover">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="HVAC Service" class="w-full service-image">
                        <div class="absolute top-3 right-3">
                            <button class="p-2 bg-white rounded-full shadow-md text-gray-600 hover:text-red-500 transition-colors">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <img src="https://randomuser.me/api/portraits/men/52.jpg" alt="James P." class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">James P.</p>
                                    <p class="text-xs text-gray-500">HVAC Technician</p>
                                </div>
                            </div>
                            <span class="price-tag">₱1,200+</span>
                        </div>
                        <a href="#" class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors block mb-3">
                            Air Conditioning Installation & Repair
                        </a>
                        <div class="flex items-center justify-between">
                            <span class="rating-badge">
                                <i class="fas fa-star mr-1"></i> 4.7 (51)
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i> 5km away
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More Button -->
            <div class="mt-10 text-center">
                <button class="px-6 py-3 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                    Load More Services
                </button>
            </div>
        </section>
    </main>

    <script src="script/home.js"></script>
    <script src="script/get_user_location.js"></script>

    <?php include 'client/profile.php'; ?>
    <?php include 'client/user_verification.php'; ?>
    <?php include 'client/worker_application.php'; ?>
    <?php include 'security.php'; ?>
</body>
</html>