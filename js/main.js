// Mobile Menu Toggle
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navMenu = document.getElementById('navMenu');

if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

// Dropdown Menu for Mobile
const dropdowns = document.querySelectorAll('.dropdown');
dropdowns.forEach(dropdown => {
    const dropdownLink = dropdown.querySelector('a');
    
    if (window.innerWidth <= 768) {
        dropdownLink.addEventListener('click', (e) => {
            e.preventDefault();
            dropdown.classList.toggle('active');
        });
    }
});

// Load News & Events on Homepage
function loadHomeNews() {
    const newsGrid = document.getElementById('newsGrid');
    if (!newsGrid) return;

    const news = getStorageData('news') || getDefaultNews();
    const latestNews = news.slice(0, 3);

    newsGrid.innerHTML = latestNews.map(item => `
        <div class="news-card">
            <img src="${item.image}" alt="${item.title}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22%3E%3Crect fill=%22%233498db%22 width=%22400%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2220%22 fill=%22white%22%3E${item.type}%3C/text%3E%3C/svg%3E'">
            <div class="news-content">
                <div class="news-meta">
                    <span><i class="fas fa-calendar"></i> ${formatDate(item.date)}</span>
                    <span><i class="fas fa-tag"></i> ${item.type}</span>
                </div>
                <h3>${item.title}</h3>
                <p>${item.description.substring(0, 100)}...</p>
            </div>
        </div>
    `).join('');
}

// Get default news data
function getDefaultNews() {
    return [
        {
            id: 1,
            title: "Admission Open for Academic Year 2025/26",
            description: "Admissions are now open for all classes including +2 Computer Science and Hotel Management programs. Limited seats available. Apply now!",
            date: "2025-01-15",
            type: "Notice",
            image: "images/news1.jpg"
        },
        {
            id: 2,
            title: "Annual Sports Day 2025",
            description: "Join us for our Annual Sports Day celebration featuring various athletic competitions, games, and exciting performances by our students.",
            date: "2025-02-10",
            type: "Event",
            image: "images/news2.jpg"
        },
        {
            id: 3,
            title: "Outstanding SEE Results 2024",
            description: "We are proud to announce that our students have achieved exceptional results in SEE 2024 with 95% distinction rate.",
            date: "2025-01-20",
            type: "Achievement",
            image: "images/news3.jpg"
        },
        {
            id: 4,
            title: "Science Exhibition 2025",
            description: "Students will showcase their innovative science projects and experiments. Parents and guests are cordially invited to attend.",
            date: "2025-03-05",
            type: "Event",
            image: "images/news4.jpg"
        },
        {
            id: 5,
            title: "Winter Vacation Notice",
            description: "School will remain closed from December 25 to January 5 for winter vacation. Classes will resume on January 6, 2025.",
            date: "2024-12-15",
            type: "Notice",
            image: "images/news5.jpg"
        }
    ];
}

// Storage helper functions
function getStorageData(key) {
    const data = localStorage.getItem(key);
    return data ? JSON.parse(data) : null;
}

function setStorageData(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
}

// Initialize default data if not exists
function initializeDefaultData() {
    if (!getStorageData('news')) {
        setStorageData('news', getDefaultNews());
    }
}

// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Form validation helper
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
            showError(input, 'This field is required');
        } else {
            input.classList.remove('error');
            removeError(input);
        }

        // Email validation
        if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                isValid = false;
                input.classList.add('error');
                showError(input, 'Please enter a valid email');
            }
        }

        // Phone validation
        if (input.type === 'tel' && input.value) {
            const phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(input.value.replace(/[-\s]/g, ''))) {
                isValid = false;
                input.classList.add('error');
                showError(input, 'Please enter a valid phone number');
            }
        }
    });

    return isValid;
}

function showError(input, message) {
    removeError(input);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    input.parentNode.insertBefore(errorDiv, input.nextSibling);
}

function removeError(input) {
    const errorDiv = input.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Show success/error messages
function showMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message message-${type}`;
    messageDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(messageDiv);

    setTimeout(() => {
        messageDiv.classList.add('show');
    }, 100);

    setTimeout(() => {
        messageDiv.classList.remove('show');
        setTimeout(() => messageDiv.remove(), 300);
    }, 3000);
}

// Generate unique ID
function generateId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeDefaultData();
    loadHomeNews();
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
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
