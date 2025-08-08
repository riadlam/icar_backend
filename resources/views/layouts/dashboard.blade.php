<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        .content-transition {
            transition: margin-left 0.3s ease-in-out;
        }
        .sidebar-item {
            transition: all 0.2s ease-in-out;
        }
        .sidebar-item:hover {
            transform: translateX(4px);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50" x-data="{ sidebarOpen: true, mobileMenuOpen: false, userMenuOpen: false }">
    <div class="flex h-full">
        <!-- Fixed Sidebar -->
        <div class="hidden lg:flex flex-col w-64 bg-white shadow-lg fixed h-full z-30">
            <!-- Logo Section -->
            <div class="gradient-bg px-6 py-8">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-car text-2xl text-indigo-600"></i>
                    </div>
                    <div>
                        <h1 class="text-white text-xl font-bold">iCar</h1>
                        <p class="text-indigo-100 text-sm">Admin Dashboard</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Main Menu</h3>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : '' }}">
                            <i class="fas fa-home w-5 h-5 mr-3"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-car w-5 h-5 mr-3"></i>
                            <span class="font-medium">Cars</span>
                            <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">12</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-cog w-5 h-5 mr-3"></i>
                            <span class="font-medium">Spare Parts</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-warehouse w-5 h-5 mr-3"></i>
                            <span class="font-medium">Garages</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-truck-pickup w-5 h-5 mr-3"></i>
                            <span class="font-medium">Tow Trucks</span>
                        </a>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Management</h3>
                    <div class="space-y-1">
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-users w-5 h-5 mr-3"></i>
                            <span class="font-medium">Users</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                            <span class="font-medium">Analytics</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-cog w-5 h-5 mr-3"></i>
                            <span class="font-medium">Settings</span>
                        </a>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Support</h3>
                    <div class="space-y-1">
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-question-circle w-5 h-5 mr-3"></i>
                            <span class="font-medium">Help Center</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-headset w-5 h-5 mr-3"></i>
                            <span class="font-medium">Contact Support</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Profile Section -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer">
                    <img class="w-10 h-10 rounded-full ring-2 ring-indigo-100" src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff&size=40" alt="Admin">
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold text-gray-900">Admin User</p>
                        <p class="text-xs text-gray-500">admin@icar.com</p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                </div>
            </div>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="lg:hidden fixed inset-0 z-40 bg-gray-600 bg-opacity-75 transition-opacity"></div>

        <!-- Mobile Sidebar -->
        <div x-show="mobileMenuOpen" class="lg:hidden fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-50 sidebar-transition">
            <div class="gradient-bg px-6 py-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-car text-2xl text-indigo-600"></i>
                        </div>
                        <div>
                            <h1 class="text-white text-xl font-bold">iCar</h1>
                            <p class="text-indigo-100 text-sm">Admin Dashboard</p>
                        </div>
                    </div>
                    <button @click="mobileMenuOpen = false" class="text-white hover:text-indigo-100">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <!-- Mobile navigation content (same as desktop but with close button) -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <div class="mb-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Main Menu</h3>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : '' }}">
                            <i class="fas fa-home w-5 h-5 mr-3"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-car w-5 h-5 mr-3"></i>
                            <span class="font-medium">Cars</span>
                            <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">12</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-cog w-5 h-5 mr-3"></i>
                            <span class="font-medium">Spare Parts</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-warehouse w-5 h-5 mr-3"></i>
                            <span class="font-medium">Garages</span>
                        </a>
                        <a href="#" class="sidebar-item flex items-center px-3 py-3 rounded-xl text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-truck-pickup w-5 h-5 mr-3"></i>
                            <span class="font-medium">Tow Trucks</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="lg:ml-64 flex-1 flex flex-col min-h-screen">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Left side -->
                        <div class="flex items-center">
                            <button @click="mobileMenuOpen = true" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            <h2 class="text-xl font-semibold text-gray-900 ml-4 lg:ml-0">
                                @yield('title', 'Dashboard')
                            </h2>
                        </div>

                        <!-- Right side -->
                        <div class="flex items-center space-x-4">
                            <!-- Search -->
                            <div class="hidden md:block">
                                <div class="relative">
                                    <input type="text" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Notifications -->
                            <div class="relative">
                                <button class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 relative">
                                    <i class="fas fa-bell text-xl"></i>
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                                </button>
                            </div>

                            <!-- User Menu -->
                            <div class="relative" x-data="{ userMenuOpen: false }">
                                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100">
                                    <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff&size=32" alt="">
                                    <span class="hidden md:block text-sm font-medium text-gray-700">Admin</span>
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </button>
                                
                                <div x-show="userMenuOpen" @click.away="userMenuOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>Profile
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i>Settings
                                    </a>
                                    <hr class="my-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
