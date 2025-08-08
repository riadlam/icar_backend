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
                    <option value="0-1000000">Under 1,000,000 DA</option>
                    <option value="1000000-2500000">1,000,000 - 2,500,000 DA</option>
                    <option value="2500000-5000000">2,500,000 - 5,000,000 DA</option>
                    <option value="5000000+">Over 5,000,000 DA</option>
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
                            <div class="text-sm font-semibold text-gray-900">{{ number_format($car['price']) }} DA</div>
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
                            {{ \Carbon\Carbon::parse($car['created_at'])->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="deleteCar({{ $car['id'] }})" class="text-red-600 hover:text-red-900 transition-colors" title="Delete Car">
                                <i class="fas fa-trash"></i>
                            </button>
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
                            case '0-1000000':
                                priceMatch = price <= 1000000;
                                break;
                            case '1000000-2500000':
                                priceMatch = price > 1000000 && price <= 2500000;
                                break;
                            case '2500000-5000000':
                                priceMatch = price > 2500000 && price <= 5000000;
                                break;
                            case '5000000+':
                                priceMatch = price > 5000000;
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
                            <div class="text-sm font-semibold text-gray-900">${new Intl.NumberFormat().format(car.price)} DA</div>
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
                            <button onclick="deleteCar(${car.id})" class="text-red-600 hover:text-red-900 transition-colors" title="Delete Car">
                                <i class="fas fa-trash"></i>
                            </button>
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

        // Delete car functionality
        function deleteCar(carId) {
            if (confirm('Are you sure you want to delete this car? This action cannot be undone.')) {
                // Show loading state
                const deleteButton = event.target.closest('button');
                const originalContent = deleteButton.innerHTML;
                deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                deleteButton.disabled = true;

                fetch(`/cars/${carId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the row from the table
                        const row = deleteButton.closest('tr');
                        row.remove();
                        
                        // Update the stats
                        updateStats();
                        
                        // Show success message
                        showNotification('Car deleted successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to delete car');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'Failed to delete car', 'error');
                    
                    // Restore button
                    deleteButton.innerHTML = originalContent;
                    deleteButton.disabled = false;
                });
            }
        }

        // Update stats after deletion
        function updateStats() {
            const totalCars = document.querySelectorAll('#carsTableBody tr').length;
            const forSaleCars = Array.from(document.querySelectorAll('#carsTableBody tr')).filter(row => 
                row.querySelector('td:nth-child(3) span').textContent.trim() === 'Sale'
            ).length;
            const forRentCars = Array.from(document.querySelectorAll('#carsTableBody tr')).filter(row => 
                row.querySelector('td:nth-child(3) span').textContent.trim() === 'Rent'
            ).length;
            const activeCars = Array.from(document.querySelectorAll('#carsTableBody tr')).filter(row => 
                row.querySelector('td:nth-child(5) span').textContent.trim() === 'Active'
            ).length;

            // Update stats cards
            document.querySelector('.grid-cols-1.md\\:grid-cols-4 > div:nth-child(1) .text-2xl').textContent = totalCars;
            document.querySelector('.grid-cols-1.md\\:grid-cols-4 > div:nth-child(2) .text-2xl').textContent = forSaleCars;
            document.querySelector('.grid-cols-1.md\\:grid-cols-4 > div:nth-child(3) .text-2xl').textContent = forRentCars;
            document.querySelector('.grid-cols-1.md\\:grid-cols-4 > div:nth-child(4) .text-2xl').textContent = activeCars;
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
@endsection
