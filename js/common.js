// js/common.js

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            mobileMenu.style.display = mobileMenu.classList.contains('hidden') ? 'none' : 'block';
        });
    }
    

    // Close modals when clicking outside
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
        
        const closeBtn = modal.querySelector('.close-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                closeModal(modal);
            });
        }
    });
    
    // Function to open modal
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
    };
    
    // Function to close modal
    window.closeModal = function(modal) {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Re-enable scrolling
    };
    
    // Add active class to current navigation link
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.main-nav a, .mobile-menu a');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage || (currentPage === '' && linkPage === 'index.php')) {
            link.classList.add('active');
        }
    });
    
    // Form validation function
    window.validateForm = function(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                markInvalid(input, 'Ce champ est obligatoire');
                isValid = false;
            } else {
                markValid(input);
                
                // Email validation
                if (input.type === 'email' && !isValidEmail(input.value)) {
                    markInvalid(input, 'Email invalide');
                    isValid = false;
                }
                
                // Phone validation
                if (input.type === 'tel' && !isValidPhone(input.value)) {
                    markInvalid(input, 'Numéro de téléphone invalide');
                    isValid = false;
                }
                
                // Password validation
                if (input.type === 'password' && input.id === 'registerPassword' && input.value.length < 8) {
                    markInvalid(input, 'Le mot de passe doit contenir au moins 8 caractères');
                    isValid = false;
                }
                
                // Confirm password validation
                if (input.id === 'confirmPassword') {
                    const password = document.getElementById('registerPassword');
                    if (password && input.value !== password.value) {
                        markInvalid(input, 'Les mots de passe ne correspondent pas');
                        isValid = false;
                    }
                }
            }
        });
        
        return isValid;
    };
    
    function markInvalid(input, message) {
        input.classList.add('invalid');
        input.classList.remove('valid');
        
        // Add or update error message
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('error-message')) {
            errorDiv = document.createElement('div');
            errorDiv.classList.add('error-message');
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }
        errorDiv.textContent = message;
    }
    
    function markValid(input) {
        input.classList.remove('invalid');
        input.classList.add('valid');
        
        // Remove error message if exists
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('error-message')) {
            errorDiv.remove();
        }
    }
    
    function isValidEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    function isValidPhone(phone) {
        // Simple validation for Cameroon phone number
        const re = /^(6|2)[0-9]{8}$/;
        return re.test(String(phone).replace(/\s/g, ''));
    }
    
    // Toast notification function
    window.showToast = function(message, type = 'success', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        
        // Add toast to the DOM
        const toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            const newContainer = document.createElement('div');
            newContainer.className = 'toast-container';
            document.body.appendChild(newContainer);
            newContainer.appendChild(toast);
        } else {
            toastContainer.appendChild(toast);
        }
        
        // Show toast
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        // Hide and remove toast after duration
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, duration);
    };
    
    // Add a debounce function for search input
    window.debounce = function(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };
});