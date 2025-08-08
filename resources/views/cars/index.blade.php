@extends('layouts.dashboard')

@section('title', 'Cars Management')

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Cars Management</h1>
                <p class="text-gray-600 mt-2">Manage all car listings in your platform</p>
            </div>
            <div class="flex items-center space-x-4">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Car
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Cars</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $cars->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-car text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">For Sale</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $cars->where('type', 'sale')->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-tag text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">For Rent</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $cars->where('type', 'rent')->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-key text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Listings</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $cars->where('enabled', true)->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="searchInput" placeholder="Search cars..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select id="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <select id="brandFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Brands</option>
                    @foreach($cars->pluck('brand')->unique()->sort() as $brand)
                        <option value="{{ $brand }}">{{ $brand }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                <select id="priceFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Prices</option>
                    <option value="0-10000">Under $10,000</option>
                    <option value="10000-25000">$10,000 - $25,000</option>
                    <option value="25000-50000">$25,000 - $50,000</option>
                    <option value="50000+">Over $50,000</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Cars Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">All Cars</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500" id="resultsCount">{{ $cars->count() }} cars found</span>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="carsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Car Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="carsTableBody">
                    @forelse($cars as $car)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                    @if($car['images'] && count($car['images']) > 0)
                                        <img src="{{ $car['images'][0] }}" alt="{{ $car['brand'] }} {{ $car['model'] }}" class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <i class="fas fa-car text-gray-600"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $car['brand'] }} {{ $car['model'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $car['year'] }} • {{ number_format($car['mileage']) }} km</div>
                                    <div class="text-xs text-gray-400">{{ $car['transmission'] }} • {{ $car['fuel'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">${{ number_format($car['price']) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $car['type'] === 'sale' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ucfirst($car['type']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $car['full_name'] ?? 'Unknown' }}</div>
                                    <div class="text-sm text-gray-500">{{ $car['mobile'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-400">{{ $car['city'] ?? 'N/A' }}</div>
                                </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $car['enabled'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $car['enabled'] ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $car['created_at']->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-car text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No cars found</p>
                                <p class="text-sm">Start by adding your first car listing.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Search and filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const typeFilter = document.getElementById('typeFilter');
            const brandFilter = document.getElementById('brandFilter');
            const priceFilter = document.getElementById('priceFilter');
            const carsTableBody = document.getElementById('carsTableBody');
            const resultsCount = document.getElementById('resultsCount');
            
            const allCars = @json($cars);
            
            function filterCars() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedType = typeFilter.value;
                const selectedBrand = brandFilter.value;
                const selectedPrice = priceFilter.value;
                
                let filteredCars = allCars.filter(car => {
                    // Search filter
                                         const searchMatch = !searchTerm || 
                         car.brand.toLowerCase().includes(searchTerm) ||
                         car.model.toLowerCase().includes(searchTerm) ||
                         (car.full_name && car.full_name.toLowerCase().includes(searchTerm));
                    
                    // Type filter
                    const typeMatch = !selectedType || car.type === selectedType;
                    
                    // Brand filter
                    const brandMatch = !selectedBrand || car.brand === selectedBrand;
                    
                    // Price filter
                    let priceMatch = true;
                    if (selectedPrice) {
                        const price = car.price;
                        switch(selectedPrice) {
                            case '0-10000':
                                priceMatch = price <= 10000;
                                break;
                            case '10000-25000':
                                priceMatch = price > 10000 && price <= 25000;
                                break;
                            case '25000-50000':
                                priceMatch = price > 25000 && price <= 50000;
                                break;
                            case '50000+':
                                priceMatch = price > 50000;
                                break;
                        }
                    }
                    
                    return searchMatch && typeMatch && brandMatch && priceMatch;
                });
                
                // Update results count
                resultsCount.textContent = `${filteredCars.length} cars found`;
                
                // Update table
                updateTable(filteredCars);
            }
            
            function updateTable(cars) {
                if (cars.length === 0) {
                    carsTableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-search text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No cars found</p>
                                    <p class="text-sm">Try adjusting your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                carsTableBody.innerHTML = cars.map(car => `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                    ${car.images && car.images.length > 0 
                                        ? `<img src="${car.images[0]}" alt="${car.brand} ${car.model}" class="w-12 h-12 rounded-lg object-cover">`
                                        : '<i class="fas fa-car text-gray-600"></i>'
                                    }
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">${car.brand} ${car.model}</div>
                                    <div class="text-sm text-gray-500">${car.year} • ${new Intl.NumberFormat().format(car.mileage)} km</div>
                                    <div class="text-xs text-gray-400">${car.transmission} • ${car.fuel}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">$${new Intl.NumberFormat().format(car.price)}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${car.type === 'sale' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800'}">
                                ${car.type.charAt(0).toUpperCase() + car.type.slice(1)}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                                             <div>
                                     <div class="text-sm font-medium text-gray-900">${car.full_name || 'Unknown'}</div>
                                     <div class="text-sm text-gray-500">${car.mobile || 'N/A'}</div>
                                     <div class="text-xs text-gray-400">${car.city || 'N/A'}</div>
                                 </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${car.enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${car.enabled ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${new Date(car.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            }
            
            // Event listeners
            searchInput.addEventListener('input', filterCars);
            typeFilter.addEventListener('change', filterCars);
            brandFilter.addEventListener('change', filterCars);
            priceFilter.addEventListener('change', filterCars);
        });
    </script>
@endsection
