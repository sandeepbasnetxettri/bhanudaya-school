// Enhanced Admission Form Handler
document.addEventListener('DOMContentLoaded', function() {
    const admissionForm = document.getElementById('enhancedAdmissionForm');
    const fileUploadWrapper = document.getElementById('fileUploadWrapper');
    const photoInput = document.getElementById('photo');
    const successMessage = document.getElementById('successMessage');
    const formContent = document.getElementById('formContent');

    // File upload handler
    if (fileUploadWrapper && photoInput) {
        fileUploadWrapper.addEventListener('click', function() {
            photoInput.click();
        });

        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // in MB
                if (fileSize > 2) {
                    alert('File size should not exceed 2MB');
                    photoInput.value = '';
                    return;
                }
                fileUploadWrapper.innerHTML = `
                    <input type="file" id="photo" name="photo" accept="image/*">
                    <p><i class="fas fa-check-circle"></i> ${file.name} selected</p>
                    <small>JPG, PNG, Max 2MB</small>
                `;
                // Reattach event listener to the new input
                document.getElementById('photo').addEventListener('change', arguments.callee);
            }
        });
    }

    // Form submission handler
    if (admissionForm) {
        admissionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simple validation
            if (!validateForm()) {
                return;
            }
            
            // Show loading state
            const submitButton = admissionForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            submitButton.disabled = true;
            
            // Simulate form submission (in a real application, this would be an AJAX call)
            setTimeout(function() {
                // Hide form and show success message
                formContent.style.display = 'none';
                successMessage.style.display = 'block';
                
                // Reset form after 5 seconds
                setTimeout(function() {
                    admissionForm.reset();
                    formContent.style.display = 'block';
                    successMessage.style.display = 'none';
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }, 5000);
            }, 2000);
        });
    }

    // Form validation function
    function validateForm() {
        let isValid = true;
        const requiredFields = admissionForm.querySelectorAll('[required]');
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
                
                // Remove error class when user starts typing
                field.addEventListener('input', function() {
                    field.classList.remove('error');
                });
            } else {
                field.classList.remove('error');
            }
        });
        
        // Email validation
        const emailField = document.getElementById('email');
        if (emailField && emailField.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value)) {
                isValid = false;
                emailField.classList.add('error');
                alert('Please enter a valid email address');
            }
        }
        
        // Phone validation
        const phoneField = document.getElementById('phone');
        if (phoneField && phoneField.value) {
            const phoneRegex = /^\+?[0-9]{10,15}$/;
            if (!phoneRegex.test(phoneField.value.replace(/[-\s()]/g, ''))) {
                isValid = false;
                phoneField.classList.add('error');
                alert('Please enter a valid phone number');
            }
        }
        
        if (!isValid) {
            alert('Please fill in all required fields correctly.');
        }
        
        return isValid;
    }

    // Timeline animation
    const timelineItems = document.querySelectorAll('.timeline-item');
    
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.5
    };
    
    const observer = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);
    
    timelineItems.forEach(function(item) {
        observer.observe(item);
    });

    // Add animation classes to timeline items
    timelineItems.forEach(function(item, index) {
        item.style.transitionDelay = (index * 0.2) + 's';
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});