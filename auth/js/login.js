// Login Form JavaScript
document.addEventListener('DOMContentLoaded', () => {
    // Initialize Supabase service
    const supabase = window.supabaseService;
    
    // Try to get form by ID first, then by tag
    const loginForm = document.getElementById('loginForm') || document.querySelector('form');
    
    // Enhanced form validation
    function validateForm(email, password) {
        const errors = [];
        
        if (!email) {
            errors.push('Email is required');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.push('Please enter a valid email address');
        }
        
        if (!password) {
            errors.push('Password is required');
        } else if (password.length < 6) {
            errors.push('Password must be at least 6 characters');
        }
        
        return errors;
    }
    
    // Show message function with enhanced styling
    function showMessage(message, type) {
        // Try to find existing message container or create one
        let messageDiv = document.getElementById('message');
        if (!messageDiv) {
            messageDiv = document.createElement('div');
            messageDiv.id = 'message';
            // Insert after the form or at the end of the auth box
            const authBox = document.querySelector('.auth-box');
            const form = document.querySelector('form');
            if (form) {
                form.parentNode.insertBefore(messageDiv, form.nextSibling);
            } else if (authBox) {
                authBox.appendChild(messageDiv);
            } else {
                document.body.appendChild(messageDiv);
            }
        }
        
        messageDiv.innerHTML = message;
        messageDiv.className = type;
        messageDiv.style.display = 'block';
        
        // Add visual feedback
        if (type === 'error') {
            messageDiv.classList.add('shake');
        }
        
        // Auto-hide message after 5 seconds
        setTimeout(() => {
            messageDiv.style.display = 'none';
            messageDiv.classList.remove('shake');
        }, 5000);
    }
    
    // Check if user is already logged in
    const currentUser = supabase && supabase.getCurrentUser ? supabase.getCurrentUser() : null;
    if (currentUser) {
        // Redirect based on user role
        if (currentUser.role === 'admin') {
            window.location.href = 'admin-dashboard.php';
        } else if (currentUser.role === 'student') {
            window.location.href = 'student-portal.php';
        } else {
            window.location.href = '../index.php';
        }
        return;
    }
    
    // Login form handler
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email') ? document.getElementById('email').value : '';
            const password = document.getElementById('password') ? document.getElementById('password').value : '';
            
            // Validate form
            const errors = validateForm(email, password);
            if (errors.length > 0) {
                showMessage('Error: ' + errors.join(', '), 'error');
                return;
            }
            
            // Add loading state to button
            const submitButton = loginForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton ? submitButton.innerHTML : '';
            if (submitButton) {
                submitButton.innerHTML = '<span class="loading-spinner"></span> Logging in...';
                submitButton.disabled = true;
            }
            
            // Check if we should use PHP or Supabase
            if (window.usePhpBackend) {
                // Authenticate user with PHP backend
                fetch('login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({email, password})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showMessage('Login successful! Redirecting...', 'success');
                        
                        // Reset button
                        if (submitButton) {
                            submitButton.innerHTML = originalButtonText;
                            submitButton.disabled = false;
                        }
                        
                        // Redirect based on role
                        const user = data.user;
                        setTimeout(() => {
                            if (user.role === 'admin') {
                                window.location.href = 'admin-dashboard.php';
                            } else if (user.role === 'student') {
                                window.location.href = 'student-portal.php';
                            } else {
                                window.location.href = '../index.php';
                            }
                        }, 1500);
                    } else {
                        showMessage('Error: ' + data.error, 'error');
                        // Reset button
                        if (submitButton) {
                            submitButton.innerHTML = originalButtonText;
                            submitButton.disabled = false;
                        }
                    }
                })
                .catch(error => {
                    showMessage('Error: ' + error.message, 'error');
                    // Reset button
                    if (submitButton) {
                        submitButton.textContent = originalButtonText;
                        submitButton.disabled = false;
                    }
                });
            } else if (supabase && supabase.signIn) {
                // Authenticate user with Supabase
                supabase.signIn(email, password)
                    .then(response => {
                        if (response.error) {
                            showMessage('Error: ' + response.error.message, 'error');
                            // Reset button
                            if (submitButton) {
                                submitButton.innerHTML = originalButtonText;
                                submitButton.disabled = false;
                            }
                        } else {
                            // Show success message
                            showMessage('Login successful! Redirecting...', 'success');
                            
                            // Reset button
                            if (submitButton) {
                                submitButton.innerHTML = originalButtonText;
                                submitButton.disabled = false;
                            }
                            
                            // Redirect based on role
                            const user = response.data.user;
                            setTimeout(() => {
                                if (user.role === 'admin') {
                                    window.location.href = 'admin-dashboard.php';
                                } else if (user.role === 'student') {
                                    window.location.href = 'student-portal.php';
                                } else {
                                    window.location.href = '../index.php';
                                }
                            }, 1500);
                        }
                    })
                    .catch(error => {
                        showMessage('Error: ' + error.message, 'error');
                        // Reset button
                        if (submitButton) {
                            submitButton.innerHTML = originalButtonText;
                            submitButton.disabled = false;
                        }
                    });
            } else {
                // Fallback for traditional form submission
                if (submitButton) {
                    submitButton.innerHTML = originalButtonText;
                    submitButton.disabled = false;
                }
                // Allow form to submit normally
                loginForm.removeEventListener('submit', this);
                loginForm.submit();
            }
        });
    }
});