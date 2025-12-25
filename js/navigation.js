// Enhanced Navigation System for Excellence School Website
// This script adds keyboard navigation, improved accessibility, and smooth transitions

document.addEventListener('DOMContentLoaded', function() {
    // Initialize navigation enhancements
    initNavigation();
});

function initNavigation() {
    // Get all navigation elements
    const navMenu = document.getElementById('navMenu');
    const navLinks = navMenu.querySelectorAll('a');
    const dropdowns = document.querySelectorAll('.dropdown');
    
    // Add keyboard navigation support
    addKeyboardNavigation(navLinks);
    
    // Add focus indicators for accessibility
    addFocusIndicators(navLinks);
    
    // Enhance dropdown behavior
    enhanceDropdowns(dropdowns);
    
    // Add active state tracking
    setActiveState();
    
    // Add smooth scrolling for anchor links
    addSmoothScrolling();
}

function addKeyboardNavigation(links) {
    // Add keyboard navigation to all menu items
    links.forEach((link, index) => {
        link.addEventListener('keydown', function(e) {
            switch(e.key) {
                case 'ArrowRight':
                    e.preventDefault();
                    const nextIndex = (index + 1) % links.length;
                    links[nextIndex].focus();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    const prevIndex = (index - 1 + links.length) % links.length;
                    links[prevIndex].focus();
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    // If this is a dropdown, open it and focus first item
                    const parentDropdown = link.closest('.dropdown');
                    if (parentDropdown) {
                        parentDropdown.classList.add('active');
                        const dropdownMenu = parentDropdown.querySelector('.dropdown-menu');
                        if (dropdownMenu) {
                            const firstItem = dropdownMenu.querySelector('a');
                            if (firstItem) firstItem.focus();
                        }
                    }
                    break;
                case 'Escape':
                    // Close any open dropdowns
                    document.querySelectorAll('.dropdown.active').forEach(dropdown => {
                        dropdown.classList.remove('active');
                    });
                    break;
            }
        });
    });
}

function addFocusIndicators(links) {
    // Add visual focus indicators for better accessibility
    links.forEach(link => {
        link.addEventListener('focus', function() {
            this.classList.add('keyboard-focused');
        });
        
        link.addEventListener('blur', function() {
            this.classList.remove('keyboard-focused');
        });
    });
}

function enhanceDropdowns(dropdowns) {
    dropdowns.forEach(dropdown => {
        const dropdownLink = dropdown.querySelector('a');
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
        
        if (!dropdownLink || !dropdownMenu) return;
        
        // Add aria attributes for accessibility
        dropdownLink.setAttribute('aria-haspopup', 'true');
        dropdownLink.setAttribute('aria-expanded', 'false');
        
        // Handle mouse events
        dropdownLink.addEventListener('click', function(e) {
            // On mobile, toggle the dropdown
            if (window.innerWidth <= 768) {
                e.preventDefault();
                dropdown.classList.toggle('active');
                dropdownLink.setAttribute('aria-expanded', dropdown.classList.contains('active'));
            }
        });
        
        // Handle focus events for keyboard navigation
        dropdownLink.addEventListener('focus', function() {
            if (window.innerWidth > 768) {
                // On desktop, show dropdown on focus
                dropdown.classList.add('active');
                dropdownLink.setAttribute('aria-expanded', 'true');
            }
        });
        
        // Handle blur events
        dropdownLink.addEventListener('blur', function() {
            // Delay hiding to allow for menu item selection
            setTimeout(() => {
                if (!dropdown.matches(':hover') && !dropdownMenu.contains(document.activeElement)) {
                    dropdown.classList.remove('active');
                    dropdownLink.setAttribute('aria-expanded', 'false');
                }
            }, 150);
        });
        
        // Handle mouseleave to close dropdown
        dropdown.addEventListener('mouseleave', function() {
            if (window.innerWidth > 768) {
                dropdown.classList.remove('active');
                dropdownLink.setAttribute('aria-expanded', 'false');
            }
        });
    });
}

function setActiveState() {
    // Set active state based on current page
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('.nav-menu a');
    
    navLinks.forEach(link => {
        // Reset active states
        link.classList.remove('active');
        
        // Check if this link matches the current page
        const linkPage = link.getAttribute('href');
        if (linkPage && (linkPage === currentPage || 
            (currentPage === 'index.html' && linkPage === './') ||
            (currentPage === 'index.html' && linkPage === '../index.html'))) {
            link.classList.add('active');
            
            // Also mark parent dropdown as active if applicable
            const parentDropdown = link.closest('.dropdown');
            if (parentDropdown) {
                const dropdownLink = parentDropdown.querySelector('a');
                if (dropdownLink) dropdownLink.classList.add('active');
            }
        }
    });
}

function addSmoothScrolling() {
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

// Function to navigate to a specific page
function navigateToPage(pageUrl) {
    window.location.href = pageUrl;
}

// Function to highlight the current page in navigation
function highlightCurrentPage() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-menu a');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        const linkHref = link.getAttribute('href');
        
        if (linkHref && linkHref.includes(currentPage)) {
            link.classList.add('active');
            
            // Highlight parent dropdown if applicable
            const parentDropdown = link.closest('.dropdown');
            if (parentDropdown) {
                const dropdownLink = parentDropdown.querySelector('a');
                if (dropdownLink) dropdownLink.classList.add('active');
            }
        }
    });
}

// Call highlight function when page loads
window.addEventListener('load', highlightCurrentPage);

// Export functions for use in other scripts
window.navigation = {
    navigateToPage: navigateToPage,
    highlightCurrentPage: highlightCurrentPage
};