/**
 * Main JavaScript for Volunteer Connect
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle functionality
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('mobile-menu-open');
                mobileMenuBtn.innerHTML = '<i class="fas fa-times text-xl"></i>';
            } else {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('mobile-menu-open');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars text-xl"></i>';
            }
        });
        
        // Close mobile menu when window resizes to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) { // md breakpoint
                mobileMenu.classList.remove('hidden');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars text-xl"></i>';
            } else if (!mobileMenu.classList.contains('mobile-menu-open')) {
                mobileMenu.classList.add('hidden');
            }
        });
    }
    
    // Reset filters button functionality
    const resetFiltersBtn = document.getElementById('resetFilters');
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = window.location.pathname;
        });
    }
    
    // Clear filters button functionality
    const clearFiltersBtn = document.getElementById('clearFilters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = window.location.pathname;
        });
    }
    
    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Get all required inputs
            const requiredInputs = form.querySelectorAll('[required]');
            
            // Remove any existing error messages
            const errorMessages = form.querySelectorAll('.form-error');
            errorMessages.forEach(msg => msg.remove());
            
            // Check each required input
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    
                    // Create error message
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'form-error';
                    errorMsg.textContent = 'This field is required';
                    
                    // Insert error message after the input
                    input.parentNode.insertBefore(errorMsg, input.nextSibling);
                    
                    // Add error style to input
                    input.classList.add('border-red-500');
                }
            });
            
            // Check email format
            const emailInputs = form.querySelectorAll('input[type="email"]');
            emailInputs.forEach(input => {
                if (input.value.trim() && !validateEmail(input.value.trim())) {
                    isValid = false;
                    
                    // Create error message
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'form-error';
                    errorMsg.textContent = 'Please enter a valid email address';
                    
                    // Insert error message after the input
                    input.parentNode.insertBefore(errorMsg, input.nextSibling);
                    
                    // Add error style to input
                    input.classList.add('border-red-500');
                }
            });
            
            // Prevent form submission if not valid
            if (!isValid) {
                e.preventDefault();
                
                // Scroll to the first error
                const firstError = form.querySelector('.form-error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
    
    // Email validation helper function
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
    
    // Social media share buttons
    const shareBtns = document.querySelectorAll('.share-btn');
    shareBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const platform = this.getAttribute('data-platform');
            const title = document.title;
            const url = window.location.href;
            
            let shareUrl = '';
            
            switch(platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
                    break;
                case 'linkedin':
                    shareUrl = `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`;
                    break;
                case 'email':
                    shareUrl = `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent('Check out this volunteer opportunity: ' + url)}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });
    
    // Initialize any tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('mouseenter', function() {
            const text = this.getAttribute('data-tooltip');
            
            const tooltipEl = document.createElement('div');
            tooltipEl.className = 'absolute z-10 bg-gray-800 text-white text-xs rounded py-1 px-2 -mt-10 -ml-2';
            tooltipEl.textContent = text;
            
            this.appendChild(tooltipEl);
        });
        
        tooltip.addEventListener('mouseleave', function() {
            const tooltipEl = this.querySelector('.absolute');
            if (tooltipEl) {
                tooltipEl.remove();
            }
        });
    });
    
    // Add animation for elements when they enter the viewport
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fadeIn');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        animatedElements.forEach(element => {
            observer.observe(element);
        });
    }
    
    // Handle print functionality
    const printBtns = document.querySelectorAll('.print-btn');
    printBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.print();
        });
    });
    
    // Add skip to content link for accessibility
    const main = document.querySelector('main') || document.querySelector('.container');
    if (main && !document.querySelector('.skip-to-content')) {
        const skipLink = document.createElement('a');
        skipLink.href = '#';
        skipLink.className = 'skip-to-content';
        skipLink.textContent = 'Skip to content';
        skipLink.addEventListener('click', function(e) {
            e.preventDefault();
            main.setAttribute('tabindex', '-1');
            main.focus();
        });
        
        document.body.insertBefore(skipLink, document.body.firstChild);
    }
});
