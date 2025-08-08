@extends('layouts.dashboard')

@section('title', 'Cars Management')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Cars Management</h1>
        <p class="text-gray-600 mt-2">Testing cars page - if you see this, the route is working!</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Test Content</h2>
        <p class="text-gray-600">This is a test page to verify the cars route is working correctly.</p>
        <p class="text-gray-600 mt-2">Route name: {{ route('cars.index') }}</p>
        <p class="text-gray-600 mt-2">Current URL: {{ request()->url() }}</p>
    </div>
@endsection
