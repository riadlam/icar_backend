@extends('layouts.dashboard')

@section('title', 'Dashboard Overview')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Welcome back, Admin! 👋</h1>
                    <p class="text-indigo-100 text-lg">Here's an overview of your iCar platform.</p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-car text-6xl text-indigo-200"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Cars</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_cars']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Car listings</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-car text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Spare Parts</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_spare_parts']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Available parts</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-cogs text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Garages</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_garages']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Service providers</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-tools text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Tow Trucks</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_tow_trucks']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Emergency services</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-truck-pickup text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Registered users</p>
                </div>
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Cities</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['active_cities']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Coverage areas</p>
                </div>
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-map-marker-alt text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('cars.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-car text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Manage Cars</span>
                </a>
                <a href="{{ route('spare-parts.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-all group">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-green-200 transition-colors">
                        <i class="fas fa-cogs text-green-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Manage Parts</span>
                </a>
                <a href="{{ route('garages.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all group">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-purple-200 transition-colors">
                        <i class="fas fa-tools text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Manage Garages</span>
                </a>
                <a href="{{ route('tow-trucks.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-yellow-300 hover:bg-yellow-50 transition-all group">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-yellow-200 transition-colors">
                        <i class="fas fa-truck-pickup text-yellow-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Manage Tow Trucks</span>
                </a>
            </div>
        </div>
    </div>
@endsection
