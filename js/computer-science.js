// Computer Science Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize page functionality
    initComputerSciencePage();
    
    // Add animation to elements when they come into view
    animateOnScroll();
});

function initComputerSciencePage() {
    console.log('Computer Science page initialized');
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.highlight-card, .subject-box, .facility-card, .career-item');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add click effects to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mousedown', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        button.addEventListener('mouseup', function() {
            this.style.transform = 'scale(1)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
}

function animateOnScroll() {
    // Simple fade-in animation for elements when they come into view
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated', 'fadeInUp');
            }
        });
    }, observerOptions);
    
    // Observe all major sections
    const sections = document.querySelectorAll('.program-highlights, .subjects-section, .topics-section, .facilities-section, .career-section');
    sections.forEach(section => {
        observer.observe(section);
    });
}

// Animation CSS (added dynamically)
function addAnimationStyles() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        .animated {
            animation-duration: 0.6s;
            animation-fill-mode: both;
        }
        
        .fadeInUp {
            animation-name: fadeInUp;
        }
    `;
    document.head.appendChild(style);
}

// Add animation styles when DOM is loaded
document.addEventListener('DOMContentLoaded', addAnimationStyles);

// Course enrollment functionality
function enrollInCourse(courseName) {
    // Show confirmation message
    showMessage(`Thank you for your interest in ${courseName}! Our admissions team will contact you shortly.`, 'success');
    
    // Scroll to contact section
    const contactSection = document.querySelector('.admission-cta');
    if (contactSection) {
        contactSection.scrollIntoView({ behavior: 'smooth' });
    }
}

// Utility function to show messages
function showMessage(message, type = 'info') {
    // Remove any existing messages
    const existingMessage = document.querySelector('.message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create message element
    const messageEl = document.createElement('div');
    messageEl.className = `message message-${type} show`;
    messageEl.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <div>${message}</div>
    `;
    
    // Add to body
    document.body.appendChild(messageEl);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        messageEl.classList.remove('show');
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.parentNode.removeChild(messageEl);
            }
        }, 300);
    }, 5000);
}

// Helper function for storage (from main.js)
function getStorageData(key) {
    try {
        return JSON.parse(localStorage.getItem(key));
    } catch (e) {
        return null;
    }
}

function setStorageData(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
        return true;
    } catch (e) {
        return false;
    }
}