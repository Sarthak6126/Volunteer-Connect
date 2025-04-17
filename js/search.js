/**
 * Search functionality for Volunteer Connect
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get search form elements
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const locationSelect = document.getElementById('location');
    const remoteCheckbox = document.getElementById('remote');
    const resetFiltersBtn = document.getElementById('resetFilters');
    
    // Get results container
    const resultsContainer = document.querySelector('.grid');
    
    // Determine the search type based on the current page
    const pageUrl = window.location.pathname;
    const searchType = pageUrl.includes('organizations.php') ? 'organizations' : 'opportunities';
    
    // Initialize debounce timer
    let debounceTimer;
    const debounceDelay = 300; // milliseconds
    
    // Function to create a loading spinner
    function createLoadingSpinner() {
        const spinner = document.createElement('div');
        spinner.className = 'loading-spinner mx-auto my-8';
        return spinner;
    }
    
    // Function to perform search with current filters
    function performSearch() {
        // Clear existing debounce timer
        clearTimeout(debounceTimer);
        
        // Set a new debounce timer
        debounceTimer = setTimeout(() => {
            // Get current filter values
            const query = searchInput ? searchInput.value : '';
            const category = categorySelect ? categorySelect.value : '';
            const location = locationSelect ? locationSelect.value : '';
            const remote = remoteCheckbox ? remoteCheckbox.checked : false;
            
            // Build query parameters
            const params = new URLSearchParams();
            params.append('type', searchType);
            if (query) params.append('query', query);
            if (category) params.append('category', category);
            if (location) params.append('location', location);
            if (remote) params.append('remote', 'true');
            
            // Show loading state
            if (resultsContainer) {
                resultsContainer.innerHTML = '';
                resultsContainer.appendChild(createLoadingSpinner());
            }
            
            // Fetch search results
            fetch(`api/search.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && resultsContainer) {
                        // Update results count
                        const countElement = document.querySelector('.mb-6 h2');
                        if (countElement) {
                            countElement.textContent = `${data.data.length} ${searchType === 'organizations' ? 'Organizations' : 'Opportunities'} Found`;
                        }
                        
                        // Clear loading state
                        resultsContainer.innerHTML = '';
                        
                        if (data.data.length === 0) {
                            // Show empty state message
                            const emptyState = document.createElement('div');
                            emptyState.className = 'bg-white rounded-lg shadow-md p-8 text-center col-span-full';
                            emptyState.innerHTML = `
                                <div class="text-5xl text-gray-300 mb-4">
                                    <i class="fas fa-${searchType === 'organizations' ? 'building' : 'search'}"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">No ${searchType} found</h3>
                                <p class="text-gray-600 mb-4">Try adjusting your search filters or check back later.</p>
                                <button id="clearFilters" class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300">Clear Filters</button>
                            `;
                            resultsContainer.appendChild(emptyState);
                            
                            // Add event listener to the clear filters button
                            document.getElementById('clearFilters').addEventListener('click', function() {
                                window.location.href = window.location.pathname;
                            });
                        } else {
                            // Render results
                            data.data.forEach(item => {
                                const card = document.createElement('div');
                                card.className = 'bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300';
                                
                                if (searchType === 'opportunities') {
                                    card.innerHTML = `
                                        <div class="p-6">
                                            <div class="flex items-start justify-between mb-3">
                                                <span class="px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm font-medium">${item.category}</span>
                                                ${item.is_remote ? '<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Remote</span>' : ''}
                                            </div>
                                            <h3 class="text-xl font-semibold text-gray-800 mb-2">${item.title}</h3>
                                            <p class="text-sm text-gray-600 mb-1">
                                                <i class="fas fa-building mr-2"></i> ${item.organization}
                                            </p>
                                            <p class="text-sm text-gray-600 mb-1">
                                                <i class="fas fa-map-marker-alt mr-2"></i> ${item.location}
                                            </p>
                                            ${item.commitment ? `<p class="text-sm text-gray-600 mb-3">
                                                <i class="fas fa-clock mr-2"></i> ${item.commitment}
                                            </p>` : ''}
                                            <p class="text-gray-700 mb-4 line-clamp-3">${item.description}</p>
                                            <div class="mt-2">
                                                <a href="${item.url}" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300">View Details</a>
                                            </div>
                                        </div>
                                    `;
                                } else {
                                    card.innerHTML = `
                                        <div class="p-6">
                                            <div class="flex items-start justify-between mb-3">
                                                <span class="px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm font-medium">${item.primary_category}</span>
                                            </div>
                                            <h3 class="text-xl font-semibold text-gray-800 mb-2">${item.name}</h3>
                                            <p class="text-sm text-gray-600 mb-1">
                                                <i class="fas fa-map-marker-alt mr-2"></i> ${item.location}
                                            </p>
                                            <p class="text-gray-700 mb-4 line-clamp-3">${item.description}</p>
                                            <div class="mt-2">
                                                <a href="${item.url}" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300">View Profile</a>
                                            </div>
                                        </div>
                                    `;
                                }
                                
                                resultsContainer.appendChild(card);
                            });
                        }
                    } else {
                        console.error('Error fetching search results:', data.message);
                        
                        // Show error message
                        if (resultsContainer) {
                            resultsContainer.innerHTML = `
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 col-span-full">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-circle text-red-500"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-red-700">
                                                Error loading results. Please try again later.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                    
                    // Show error message
                    if (resultsContainer) {
                        resultsContainer.innerHTML = `
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 col-span-full">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-red-700">
                                            Error loading results. Please try again later.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });
        }, debounceDelay);
    }
    
    // Add event listeners to search form elements
    if (searchInput) {
        searchInput.addEventListener('input', performSearch);
    }
    
    if (categorySelect) {
        categorySelect.addEventListener('change', performSearch);
    }
    
    if (locationSelect) {
        locationSelect.addEventListener('change', performSearch);
    }
    
    if (remoteCheckbox) {
        remoteCheckbox.addEventListener('change', performSearch);
    }
    
    // Add event listener to reset filters button
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Reset form elements
            if (filterForm) {
                filterForm.reset();
            }
            
            // Update URL
            history.replaceState({}, document.title, window.location.pathname);
            
            // Perform search with reset filters
            performSearch();
        });
    }
    
    // Handle form submission
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
    }
});
