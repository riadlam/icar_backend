@extends('layouts.dashboard')

@section('title', 'Spare Parts Management')

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Spare Parts Management</h1>
            <p class="text-gray-600 mt-2">Manage all spare parts listings in your platform</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Parts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $spareParts->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-cogs text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">New Parts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $spareParts->where('condition', 'new')->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-star text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Used Parts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $spareParts->where('condition', 'used')->count() }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-recycle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Available</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $spareParts->where('is_available', true)->count() }}</p>
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
                <input type="text" id="searchInput" placeholder="Search parts..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Condition</label>
                <select id="conditionFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Conditions</option>
                    <option value="new">New</option>
                    <option value="used">Used</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <select id="brandFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Brands</option>
                    @foreach($spareParts->pluck('brand')->unique()->sort() as $brand)
                        @if($brand)
                            <option value="{{ $brand }}">{{ $brand }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                <select id="priceFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Prices</option>
                    <option value="0-50000">Under 50,000 DA</option>
                    <option value="50000-100000">50,000 - 100,000 DA</option>
                    <option value="100000-200000">100,000 - 200,000 DA</option>
                    <option value="200000+">Over 200,000 DA</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Spare Parts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">All Spare Parts</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500" id="resultsCount">{{ $spareParts->count() }} parts found</span>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="sparePartsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="sparePartsTableBody">
                    @forelse($spareParts as $part)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                    @if($part['images'] && count($part['images']) > 0)
                                        <img src="{{ $part['images'][0] }}" alt="{{ $part['title'] }}" class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <i class="fas fa-cogs text-gray-600"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $part['title'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $part['brand'] ?? 'N/A' }} {{ $part['model'] ?? '' }}</div>
                                    <div class="text-xs text-gray-400">{{ $part['year'] ?? 'N/A' }} • {{ Str::limit($part['description'], 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $part['price'] > 0 ? number_format($part['price']) . ' DA' : 'Price on request' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $part['condition'] === 'new' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ucfirst($part['condition'] ?? 'Unknown') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $part['store_name'] ?? 'Unknown Store' }}</div>
                                <div class="text-sm text-gray-500">{{ $part['mobile'] ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $part['city'] ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $part['is_available'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $part['is_available'] ? 'Available' : 'Sold' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($part['created_at'])->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="deleteSparePart({{ $part['id'] }})" class="text-red-600 hover:text-red-900 transition-colors" title="Delete Part">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-cogs text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No spare parts found</p>
                                <p class="text-sm">Start by adding your first spare part listing.</p>
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
            const conditionFilter = document.getElementById('conditionFilter');
            const brandFilter = document.getElementById('brandFilter');
            const priceFilter = document.getElementById('priceFilter');
            const sparePartsTableBody = document.getElementById('sparePartsTableBody');
            const resultsCount = document.getElementById('resultsCount');
            
            const allSpareParts = @json($spareParts);
            
            function filterSpareParts() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCondition = conditionFilter.value;
                const selectedBrand = brandFilter.value;
                const selectedPrice = priceFilter.value;
                
                let filteredParts = allSpareParts.filter(part => {
                    // Search filter
                                         const searchMatch = !searchTerm || 
                         part.title.toLowerCase().includes(searchTerm) ||
                         (part.brand && part.brand.toLowerCase().includes(searchTerm)) ||
                         (part.model && part.model.toLowerCase().includes(searchTerm)) ||
                         (part.store_name && part.store_name.toLowerCase().includes(searchTerm));
                    
                    // Condition filter
                    const conditionMatch = !selectedCondition || part.condition === selectedCondition;
                    
                    // Brand filter
                    const brandMatch = !selectedBrand || part.brand === selectedBrand;
                    
                                         // Price filter
                     let priceMatch = true;
                     if (selectedPrice) {
                         const price = part.price || 0;
                         switch(selectedPrice) {
                             case '0-50000':
                                 priceMatch = price <= 50000;
                                 break;
                             case '50000-100000':
                                 priceMatch = price > 50000 && price <= 100000;
                                 break;
                             case '100000-200000':
                                 priceMatch = price > 100000 && price <= 200000;
                                 break;
                             case '200000+':
                                 priceMatch = price > 200000;
                                 break;
                         }
                     }
                    
                    return searchMatch && conditionMatch && brandMatch && priceMatch;
                });
                
                // Update results count
                resultsCount.textContent = `${filteredParts.length} parts found`;
                
                // Update table
                updateTable(filteredParts);
            }
            
            function updateTable(parts) {
                if (parts.length === 0) {
                    sparePartsTableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-search text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No spare parts found</p>
                                    <p class="text-sm">Try adjusting your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                sparePartsTableBody.innerHTML = parts.map(part => `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                    ${part.images && part.images.length > 0 
                                        ? `<img src="${part.images[0]}" alt="${part.title}" class="w-12 h-12 rounded-lg object-cover">`
                                        : '<i class="fas fa-cogs text-gray-600"></i>'
                                    }
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">${part.title}</div>
                                    <div class="text-sm text-gray-500">${part.brand || 'N/A'} ${part.model || ''}</div>
                                    <div class="text-xs text-gray-400">${part.year || 'N/A'} • ${part.description ? part.description.substring(0, 30) + (part.description.length > 30 ? '...' : '') : 'N/A'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">${part.price > 0 ? new Intl.NumberFormat().format(part.price) + ' DA' : 'Price on request'}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${part.condition === 'new' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800'}">
                                ${(part.condition || 'Unknown').charAt(0).toUpperCase() + (part.condition || 'Unknown').slice(1)}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div>
                                    <div class="text-sm font-medium text-gray-900">${part.store_name || 'Unknown Store'}</div>
                                    <div class="text-sm text-gray-500">${part.mobile || 'N/A'}</div>
                                    <div class="text-xs text-gray-400">${part.city || 'N/A'}</div>
                                </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${part.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${part.is_available ? 'Available' : 'Sold'}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${new Date(part.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="deleteSparePart(${part.id})" class="text-red-600 hover:text-red-900 transition-colors" title="Delete Part">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
            
            // Event listeners
            searchInput.addEventListener('input', filterSpareParts);
            conditionFilter.addEventListener('change', filterSpareParts);
            brandFilter.addEventListener('change', filterSpareParts);
            priceFilter.addEventListener('change', filterSpareParts);
        });

        // Delete spare part functionality
        function deleteSparePart(partId) {
            if (confirm('Are you sure you want to delete this spare part? This action cannot be undone.')) {
                // Show loading state
                const deleteButton = event.target.closest('button');
                const originalContent = deleteButton.innerHTML;
                deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                deleteButton.disabled = true;

                fetch(`/spare-parts/${partId}`, {
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
                        showNotification('Spare part deleted successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to delete spare part');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'Failed to delete spare part', 'error');
                    
                    // Restore button
                    deleteButton.innerHTML = originalContent;
                    deleteButton.disabled = false;
                });
            }
        }

        // Update stats after deletion
        function updateStats() {
            const totalParts = document.querySelectorAll('#sparePartsTableBody tr').length;
            const newParts = Array.from(document.querySelectorAll('#sparePartsTableBody tr')).filter(row => 
                row.querySelector('td:nth-child(3) span').textContent.trim() === 'New'
            ).length;
            const usedParts = Array.from(document.querySelectorAll('#sparePartsTableBody tr')).filter(row => 
                row.querySelector('td:nth-child(3) span').textContent.trim() === 'Used'
            ).length;
            const availableParts = Array.from(document.querySelectorAll('#sparePartsTableBody tr')).filter(row => 
                row.querySelector('td:nth-child(5) span').textContent.trim() === 'Available'
            ).length;

            // Update stats cards
            document.querySelector('.grid-cols-1.md\\:grid-cols-4 > div:nth-child(1) .text-2xl').textContent = totalParts;
            document.querySelector('.grid-cols-1.md\\:grid-cols-4 > div:nth-child(2) .text-2xl').textContent = newParts;
            document.querySelector('.grid-cols-1.md\\:grid-cols-4 > div:nth-child(3) .text-2xl').textContent = usedParts;
            document.querySelector('.grid-cols-1.md\\:grid-cols-4 > div:nth-child(4) .text-2xl').textContent = availableParts;
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
