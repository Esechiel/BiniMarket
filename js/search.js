// js/search.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize search page
    initializeSearchPage();
    
    // Filter toggle on mobile
    const filterToggle = document.getElementById('filterToggle');
    const filters = document.querySelector('.filters');
    
    if (filterToggle && filters) {
        filterToggle.addEventListener('click', function() {
            filters.classList.toggle('active');
            
            if (filters.classList.contains('active')) {
                filterToggle.innerHTML = '<i class="fas fa-times"></i> Fermer';
            } else {
                filterToggle.innerHTML = '<i class="fas fa-filter"></i> Filtres';
            }
        });
    }
    
    // Search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Get search query from URL
        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get('q');
        
        if (query) {
            searchInput.value = query;
            document.getElementById('searchTerms').textContent = query;
        }
        
        // Add search input handler with debounce
        searchInput.addEventListener('input', window.debounce(function() {
            if (this.value.length >= 2) {
                searchProducts(this.value);
            }
        }, 500));
    }
    
    // Price range slider
    const priceRange = document.getElementById('priceRange');
    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    
    if (priceRange && minPrice && maxPrice) {
        // Update input fields when slider changes
        priceRange.addEventListener('input', function() {
            maxPrice.value = this.value;
        });
        
        // Update slider when min price changes
        minPrice.addEventListener('change', function() {
            if (parseInt(this.value) > parseInt(maxPrice.value)) {
                maxPrice.value = this.value;
            }
            updatePriceRange();
        });
        
        // Update slider when max price changes
        maxPrice.addEventListener('change', function() {
            updatePriceRange();
        });
        
        function updatePriceRange() {
            const min = minPrice.value ? parseInt(minPrice.value) : 0;
            const max = maxPrice.value ? parseInt(maxPrice.value) : 100000;
            
            priceRange.value = max;
            
            // In a real app, this would update the search results
        }
    }
    
    // Apply filters button
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            applyFilters();
        });
    }
    
    // Reset filters button
    const resetFiltersBtn = document.getElementById('resetFilters');
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            resetFilters();
        });
    }
    
    // Sorting select
    const sortSelect = document.getElementById('sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortResults(this.value);
        });
    }
    
    // Pagination buttons
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const paginationNumbers = document.querySelectorAll('.pagination-number');
    
    if (prevPageBtn && nextPageBtn && paginationNumbers.length) {
        prevPageBtn.addEventListener('click', function() {
            if (!this.disabled) {
                changePage('prev');
            }
        });
        
        nextPageBtn.addEventListener('click', function() {
            changePage('next');
        });
        
        paginationNumbers.forEach(btn => {
            btn.addEventListener('click', function() {
                const page = parseInt(this.textContent);
                changePage(page);
            });
        });
    }
});


// Initialize search page
function initializeSearchPage() {
    // Get query parameters from URL
    const urlParams = new URLSearchParams(window.location.search);
    const query = urlParams.get('q');
    const category = urlParams.get('category');
    const type = urlParams.get('type');
    
    // Set filters based on URL parameters
    if (category) {
        const categoryCheckboxes = document.querySelectorAll('input[name="category"]');
        categoryCheckboxes.forEach(checkbox => {
            if (checkbox.value === category) {
                checkbox.checked = true;
            }
        });
    }
    
    if (type) {
        const typeCheckboxes = document.querySelectorAll('input[name="type"]');
        typeCheckboxes.forEach(checkbox => {
            checkbox.checked = checkbox.value === type;
        });
    }
    
    // Set search terms text
    const searchTermsElement = document.getElementById('searchTerms');
    if (searchTermsElement) {
        if (query) {
            searchTermsElement.textContent = query;
        } else if (category) {
            searchTermsElement.textContent = getCategoryName(category);
        } else {
            searchTermsElement.textContent = 'tous les produits';
        }
    }
    // À ajouter dans la fonction initializeSearchPage() de search.js
    document.addEventListener('click', function(e) {
        const resultCard = e.target.closest('.result-card');
        if (resultCard) {
            const productId = resultCard.dataset.id;
            if (productId) {
                window.location.href = `product-details.php?id=${productId}`;
            }
        }
    });
}

// Function to apply filters
function applyFilters() {
    // In a real app, this would send a filtered search request to the server
    // For demo purposes, we'll just simulate a filtering delay
    
    const resultsContainer = document.getElementById('searchResultsGrid');
    
    if (resultsContainer) {
        // Show loading state
        resultsContainer.innerHTML = '<div class="loading-spinner"></div>';
        
        // Simulate API delay
        setTimeout(() => {
            loadSearchResults();
            
            // If on mobile, close the filters panel
            const filters = document.querySelector('.filters');
            if (filters && filters.classList.contains('active')) {
                filters.classList.remove('active');
                
                const filterToggle = document.getElementById('filterToggle');
                if (filterToggle) {
                    filterToggle.innerHTML = '<i class="fas fa-filter"></i> Filtres';
                }
            }
            
            showToast('Filtres appliqués');
        }, 500);
    }
}

// Function to reset filters
function resetFilters() {
    // Reset checkboxes
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = checkbox.value === 'product' || checkbox.value === 'service';
    });
    
    // Reset price range
    const priceRange = document.getElementById('priceRange');
    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    
    if (priceRange) priceRange.value = 100000;
    if (minPrice) minPrice.value = '';
    if (maxPrice) maxPrice.value = '';
    
    // Reset location filter
    const locationFilter = document.getElementById('locationFilter');
    if (locationFilter) locationFilter.value = '';
    
    // Apply the reset filters
    applyFilters();
    
    showToast('Filtres réinitialisés');
}

// Function to sort results
function sortResults(sortType) {
    // In a real app, this would change the order of the results
    // For demo purposes, we'll just simulate a sorting delay
    
    const resultsContainer = document.getElementById('searchResultsGrid');
    
    if (resultsContainer) {
        // Show loading state
        resultsContainer.innerHTML = '<div class="loading-spinner"></div>';
        
        // Simulate API delay
        setTimeout(() => {
            loadSearchResults();
            showToast(`Résultats triés par: ${getSortName(sortType)}`);
        }, 300);
    }
}

// Function to change page
function changePage(page) {
    const paginationNumbers = document.querySelectorAll('.pagination-number');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    
    // In a real app, this would fetch results for the specified page
    
    if (page === 'prev') {
        // Go to previous page
        for (let i = 0; i < paginationNumbers.length; i++) {
            if (paginationNumbers[i].classList.contains('active')) {
                if (i > 0) {
                    paginationNumbers[i].classList.remove('active');
                    paginationNumbers[i-1].classList.add('active');
                    
                    // Enable/disable pagination buttons
                    if (i-1 === 0) {
                        prevPageBtn.disabled = true;
                        prevPageBtn.classList.add('disabled');
                    } else {
                        prevPageBtn.disabled = false;
                        prevPageBtn.classList.remove('disabled');
                    }
                    
                    nextPageBtn.disabled = false;
                    nextPageBtn.classList.remove('disabled');
                }
                break;
            }
        }
    } else if (page === 'next') {
        // Go to next page
        for (let i = 0; i < paginationNumbers.length; i++) {
            if (paginationNumbers[i].classList.contains('active')) {
                if (i < paginationNumbers.length - 1) {
                    paginationNumbers[i].classList.remove('active');
                    paginationNumbers[i+1].classList.add('active');
                    
                    // Enable/disable pagination buttons
                    prevPageBtn.disabled = false;
                    prevPageBtn.classList.remove('disabled');
                    
                    if (i+1 === paginationNumbers.length - 1) {
                        nextPageBtn.disabled = true;
                        nextPageBtn.classList.add('disabled');
                    } else {
                        nextPageBtn.disabled = false;
                        nextPageBtn.classList.remove('disabled');
                    }
                }
                break;
            }
        }
    } else {
        // Go to specific page
        paginationNumbers.forEach((btn, index) => {
            btn.classList.remove('active');
            if (parseInt(btn.textContent) === page) {
                btn.classList.add('active');
                
                // Enable/disable pagination buttons
                prevPageBtn.disabled = index === 0;
                prevPageBtn.classList.toggle('disabled', index === 0);
                
                nextPageBtn.disabled = index === paginationNumbers.length - 1;
                nextPageBtn.classList.toggle('disabled', index === paginationNumbers.length - 1);
            }
        });
    }
    
    // Show loading state
    const resultsContainer = document.getElementById('searchResultsGrid');
    if (resultsContainer) {
        resultsContainer.innerHTML = '<div class="loading-spinner"></div>';
        
        // Simulate API delay for new page
        setTimeout(() => {
            loadSearchResults();
        }, 300);
    }
    
    // Scroll to top of results
    const searchContainer = document.querySelector('.search-results');
    if (searchContainer) {
        searchContainer.scrollIntoView({ behavior: 'smooth' });
    }
}

// Helper function to get category name
function getCategoryName(category) {
    const categories = {
        'electronics': 'Électronique',
        'books': 'Livres',
        'clothes': 'Vêtements',
        'food': 'Nourriture',
        'services': 'Services',
        'furniture': 'Mobilier',
        'transport': 'Transport',
        'other': 'Autres'
    };
    
    return categories[category] || category;
}

// Helper function to get sort name
function getSortName(sortType) {
    const sortNames = {
        'recent': 'Plus récent',
        'price-asc': 'Prix croissant',
        'price-desc': 'Prix décroissant',
        'popularity': 'Popularité'
    };
    
    return sortNames[sortType] || sortType;
}

// Toast notification function
function showToast(message, type = 'success') {
    // Check if the window object has the showToast function (from common.js)
    if (window.showToast) {
        window.showToast(message, type);
    } else {
        // Fallback if the function is not available
        console.log(message);
    }
}
