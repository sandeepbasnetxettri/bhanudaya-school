// Multi-step Signup Form JavaScript
document.addEventListener('DOMContentLoaded', () => {
    // Initialize Supabase service
    const supabase = window.supabaseService;
    const form = document.getElementById('multiStepForm');
    const steps = document.querySelectorAll('.form-step');
    const progress = document.getElementById('progress');
    const currentStepSpan = document.getElementById('currentStep');
    const nextButtons = document.querySelectorAll('.next-btn');
    const prevButtons = document.querySelectorAll('.prev-btn');
    
    // Avatar preview functionality
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarImg = avatarPreview ? avatarPreview.querySelector('img') : null;
    
    if (avatarInput && avatarImg) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarImg.src = e.target.result;
                    avatarImg.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                avatarImg.style.display = 'none';
            }
        });
    }
    
    let currentStep = 0;
    
    // Initialize form
    showStep(currentStep);
    
    // Next button event listeners
    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });
    
    // Previous button event listeners
    prevButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentStep--;
            showStep(currentStep);
        });
    });
    
    // Form submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if (validateStep(currentStep)) {
            // Collect all form data
            const formData = new FormData(form);
            const userData = {};
            
            // Handle checkboxes separately to ensure arrays
            const checkboxes = form.querySelectorAll('input[type="checkbox"]:checked');
            const checkboxGroups = {};
            
            checkboxes.forEach(checkbox => {
                if (!checkboxGroups[checkbox.name]) {
                    checkboxGroups[checkbox.name] = [];
                }
                checkboxGroups[checkbox.name].push(checkbox.value);
            });
            
            // Merge checkbox data
            Object.assign(userData, checkboxGroups);
            
            // Convert remaining FormData to object
            for (let [key, value] of formData.entries()) {
                // Only add non-checkbox fields or if not already processed
                if (!userData.hasOwnProperty(key)) {
                    userData[key] = value;
                }
            }
            
            // Check if we should use PHP or Supabase
            if (window.usePhpBackend) {
                // Handle avatar upload
                const avatarInput = document.getElementById('avatar');
                
                if (avatarInput && avatarInput.files[0]) {
                    // Use FormData for file uploads
                    const formData = new FormData();
                    
                    // Append all user data
                    for (const key in userData) {
                        formData.append(key, userData[key]);
                    }
                    
                    // Append avatar file
                    formData.append('avatar', avatarInput.files[0]);
                    
                    // Register user with PHP backend using FormData
                    fetch('../php/register.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showMessage('Account created successfully! Redirecting to login...', 'success');
                            setTimeout(() => {
                                window.location.href = 'login.html';
                            }, 2000);
                        } else {
                            showMessage('Error: ' + data.error, 'error');
                        }
                    })
                    .catch(error => {
                        showMessage('Error: ' + error.message, 'error');
                    });
                } else {
                    // No avatar, use JSON
                    fetch('../php/register.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(userData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showMessage('Account created successfully! Redirecting to login...', 'success');
                            setTimeout(() => {
                                window.location.href = 'login.html';
                            }, 2000);
                        } else {
                            showMessage('Error: ' + data.error, 'error');
                        }
                    })
                    .catch(error => {
                        showMessage('Error: ' + error.message, 'error');
                    });
                }
            } else {
                // Register user with Supabase
                supabase.signUp(userData)
                    .then(response => {
                        if (response.error) {
                            showMessage('Error: ' + response.error.message, 'error');
                        } else {
                            // Show success message
                            showMessage('Account created successfully! Redirecting to login...', 'success');
                            
                            // Redirect to login page after delay
                            setTimeout(() => {
                                window.location.href = 'login.html';
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        showMessage('Error: ' + error.message, 'error');
                    });
            }
        }
    });
    
    function showStep(stepIndex) {
        // Hide all steps
        steps.forEach(step => step.classList.remove('active'));
        
        // Show current step
        steps[stepIndex].classList.add('active');
        
        // Update progress bar
        const progressPercent = ((stepIndex + 1) / steps.length) * 100;
        progress.style.width = `${progressPercent}%`;
        
        // Update step indicator
        currentStepSpan.textContent = stepIndex + 1;
    }
    
    function validateStep(stepIndex) {
        const currentStepElement = steps[stepIndex];
        const inputs = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        // Clear previous errors
        currentStepElement.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        currentStepElement.querySelectorAll('.error-message').forEach(el => el.remove());
        
        // Validate required fields
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('error');
                showError(input, 'This field is required');
            } else {
                // Additional validation for specific fields
                if (input.type === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value)) {
                        isValid = false;
                        input.classList.add('error');
                        showError(input, 'Please enter a valid email address');
                    }
                }
                
                if (input.type === 'password' && input.id === 'password') {
                    if (input.value.length < 6) {
                        isValid = false;
                        input.classList.add('error');
                        showError(input, 'Password must be at least 6 characters');
                    }
                }
                
                if (input.id === 'confirmPassword') {
                    const password = document.getElementById('password').value;
                    if (input.value !== password) {
                        isValid = false;
                        input.classList.add('error');
                        showError(input, 'Passwords do not match');
                    }
                }
            }
        });
        
        // Validate checkbox groups
        const checkboxGroups = currentStepElement.querySelectorAll('.checkbox-group');
        checkboxGroups.forEach(group => {
            const checkboxes = group.querySelectorAll('input[type="checkbox"]');
            const checked = Array.from(checkboxes).some(cb => cb.checked);
            
            if (checkboxes.length > 0 && !checked) {
                isValid = false;
                const groupName = checkboxes[0].name;
                const groupLabel = group.closest('.form-group').querySelector('label');
                if (groupLabel) {
                    showError(groupLabel, 'Please select at least one option');
                }
            }
        });
        
        // Validate radio groups
        const radioGroups = currentStepElement.querySelectorAll('.radio-group');
        radioGroups.forEach(group => {
            const radios = group.querySelectorAll('input[type="radio"]');
            const checked = Array.from(radios).some(radio => radio.checked);
            
            if (radios.length > 0 && !checked) {
                isValid = false;
                const groupName = radios[0].name;
                const groupLabel = group.closest('.form-group').querySelector('label');
                if (groupLabel) {
                    showError(groupLabel, 'Please select an option');
                }
            }
        });
        
        return isValid;
    }
    
    function showError(element, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        element.parentNode.insertBefore(errorDiv, element.nextSibling);
    }
});