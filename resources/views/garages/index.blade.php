@extends('layouts.dashboard')

@section('title', 'Garages Management')

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Garages Management</h1>
            <p class="text-gray-600 mt-2">Manage all garage profiles in your platform</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Garages</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $garages->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-tools text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Cities</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $garages->pluck('city')->unique()->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-map-marker-alt text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Services</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $garages->pluck('services')->flatten()->unique()->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-wrench text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="searchInput" placeholder="Search garages..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                <select id="cityFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Cities</option>
                    @foreach($garages->pluck('city')->unique()->sort() as $city)
                        @if($city)
                            <option value="{{ $city }}">{{ $city }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                <select id="serviceFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Services</option>
                    @foreach($garages->pluck('services')->flatten()->unique()->sort() as $service)
                        @if($service)
                            <option value="{{ $service }}">{{ $service }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Garages Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">All Garages</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500" id="resultsCount">{{ $garages->count() }} garages found</span>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="garagesTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Garage Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Services</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="garagesTableBody">
                    @forelse($garages as $garage)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                    <i class="fas fa-tools text-gray-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $garage['business_name'] ?? 'Unknown Business' }}</div>
                                    <div class="text-sm text-gray-500">{{ $garage['mechanic_name'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-400">{{ $garage['city'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $garage['business_name'] ?? 'Unknown Business' }}</div>
                                <div class="text-sm text-gray-500">{{ $garage['mobile'] ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $garage['city'] ?? 'N/A' }}</div>
                            </div>
                        </td>
                                                 <td class="px-6 py-4">
                             <div class="flex flex-wrap gap-1">
                                 @if($garage['services'] && is_array($garage['services']))
                                     @foreach($garage['services'] as $service)
                                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                             {{ $service }}
                                         </span>
                                     @endforeach
                                 @else
                                     <span class="text-sm text-gray-500">No services listed</span>
                                 @endif
                             </div>
                         </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($garage['created_at'])->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="deleteGarage({{ $garage['id'] }})" class="text-red-600 hover:text-red-900 transition-colors" title="Delete Garage">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-tools text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No garages found</p>
                                <p class="text-sm">Start by adding your first garage profile.</p>
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
            const cityFilter = document.getElementById('cityFilter');
            const serviceFilter = document.getElementById('serviceFilter');
            const garagesTableBody = document.getElementById('garagesTableBody');
            const resultsCount = document.getElementById('resultsCount');
            
            const allGarages = @json($garages);
            
            function filterGarages() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCity = cityFilter.value;
                const selectedService = serviceFilter.value;
                
                let filteredGarages = allGarages.filter(garage => {
                    // Search filter
                    const searchMatch = !searchTerm || 
                        (garage.business_name && garage.business_name.toLowerCase().includes(searchTerm)) ||
                        (garage.mechanic_name && garage.mechanic_name.toLowerCase().includes(searchTerm)) ||
                        (garage.city && garage.city.toLowerCase().includes(searchTerm));
                    
                    // City filter
                    const cityMatch = !selectedCity || garage.city === selectedCity;
                    
                    // Service filter
                    let serviceMatch = true;
                    if (selectedService && garage.services && Array.isArray(garage.services)) {
                        serviceMatch = garage.services.includes(selectedService);
                    }
                    
                    return searchMatch && cityMatch && serviceMatch;
                });
                
                // Update results count
                resultsCount.textContent = `${filteredGarages.length} garages found`;
                
                // Update table
                updateTable(filteredGarages);
            }
            
            function updateTable(garages) {
                if (garages.length === 0) {
                    garagesTableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-search text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No garages found</p>
                                    <p class="text-sm">Try adjusting your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                garagesTableBody.innerHTML = garages.map(garage => `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                    <i class="fas fa-tools text-gray-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">${garage.business_name || 'Unknown Business'}</div>
                                    <div class="text-sm text-gray-500">${garage.mechanic_name || 'N/A'}</div>
                                    <div class="text-xs text-gray-400">${garage.city || 'N/A'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">${garage.business_name || 'Unknown Business'}</div>
                                <div class="text-sm text-gray-500">${garage.mobile || 'N/A'}</div>
                                <div class="text-xs text-gray-400">${garage.city || 'N/A'}</div>
                            </div>
                        </td>
                                                 <td class="px-6 py-4">
                             <div class="flex flex-wrap gap-1">
                                 ${garage.services && Array.isArray(garage.services) && garage.services.length > 0 
                                     ? garage.services.map(service => 
                                         `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${service}</span>`
                                     ).join('')
                                     : '<span class="text-sm text-gray-500">No services listed</span>'
                                 }
                             </div>
                         </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${new Date(garage.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="deleteGarage(${garage.id})" class="text-red-600 hover:text-red-900 transition-colors" title="Delete Garage">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
            
            // Event listeners
            searchInput.addEventListener('input', filterGarages);
            cityFilter.addEventListener('change', filterGarages);
            serviceFilter.addEventListener('change', filterGarages);
        });

        // Delete garage functionality
        function deleteGarage(garageId) {
            if (confirm('Are you sure you want to delete this garage? This action cannot be undone.')) {
                // Show loading state
                const deleteButton = event.target.closest('button');
                const originalContent = deleteButton.innerHTML;
                deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                deleteButton.disabled = true;

                fetch(`/garages/${garageId}`, {
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
                        showNotification('Garage deleted successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to delete garage');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'Failed to delete garage', 'error');
                    
                    // Restore button
                    deleteButton.innerHTML = originalContent;
                    deleteButton.disabled = false;
                });
            }
        }

        // Update stats after deletion
        function updateStats() {
            const totalGarages = document.querySelectorAll('#garagesTableBody tr').length;
            const activeCities = new Set(Array.from(document.querySelectorAll('#garagesTableBody tr')).map(row => 
                row.querySelector('td:nth-child(1) .text-xs.text-gray-400').textContent.trim()
            )).size;
            const totalServices = new Set(Array.from(document.querySelectorAll('#garagesTableBody tr')).map(row => 
                Array.from(row.querySelectorAll('td:nth-child(3) .bg-blue-100')).map(span => span.textContent.trim())
            ).flat()).size;

            // Update stats cards
            document.querySelector('.grid-cols-1.md\\:grid-cols-3 > div:nth-child(1) .text-2xl').textContent = totalGarages;
            document.querySelector('.grid-cols-1.md\\:grid-cols-3 > div:nth-child(2) .text-2xl').textContent = activeCities;
            document.querySelector('.grid-cols-1.md\\:grid-cols-3 > div:nth-child(3) .text-2xl').textContent = totalServices;
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
