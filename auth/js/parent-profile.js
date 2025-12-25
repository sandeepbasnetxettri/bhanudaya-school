// Parent Profile JavaScript

document.addEventListener('DOMContentLoaded', () => {
    // Profile navigation
    const navLinks = document.querySelectorAll('.profile-nav a');
    const sections = document.querySelectorAll('.profile-section');
    
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Remove active class from all links
            navLinks.forEach(l => l.parentElement.classList.remove('active'));
            
            // Add active class to clicked link
            link.parentElement.classList.add('active');
            
            // Get target section
            const targetId = link.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            
            // Hide all sections with enhanced 3D exit animation
            sections.forEach(section => {
                if (section.classList.contains('active')) {
                    section.style.animation = 'sectionExit 0.8s cubic-bezier(0.25, 0.8, 0.25, 1) forwards';
                    setTimeout(() => {
                        section.classList.remove('active');
                        section.style.animation = '';
                    }, 800);
                }
            });
            
            // Show target section with enhanced 3D entrance animation
            setTimeout(() => {
                targetSection.classList.add('active');
                // Add floating effect to elements in the section
                animateSectionElements(targetSection);
            }, 80);
        });
    });
    
    // Child accordion functionality with enhanced 3D effects
    const expandButtons = document.querySelectorAll('.expand-btn');
    expandButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            const childSection = button.closest('.child-section');
            const childContent = childSection.querySelector('.child-content');
            const icon = button.querySelector('i');
            
            if (childContent.style.display === 'block') {
                // Collapse with enhanced 3D animation
                childContent.style.animation = 'contentCollapse 0.6s cubic-bezier(0.25, 0.8, 0.25, 1) forwards';
                setTimeout(() => {
                    childContent.style.display = 'none';
                    childContent.style.animation = '';
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                    childSection.classList.remove('expanded');
                }, 600);
            } else {
                // Expand with enhanced 3D animation
                childContent.style.display = 'block';
                childContent.style.animation = 'contentReveal 0.7s cubic-bezier(0.25, 0.8, 0.25, 1) forwards';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
                childSection.classList.add('expanded');
                
                // Add staggered animation to child elements
                setTimeout(() => {
                    animateChildSectionElements(childContent, index);
                }, 300);
            }
        });
    });
    
    // Form submission
    const personalInfoForm = document.getElementById('personalInfoForm');
    if (personalInfoForm) {
        personalInfoForm.addEventListener('submit', (e) => {
            e.preventDefault();
            showMessage('Profile information updated successfully!', 'success');
            // In a real app, you would send the data to the server here
        });
    }
    
    // Listen for avatar upload success message
    window.addEventListener('load', () => {
        // Check for avatar success message from server
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('avatar_success')) {
            showMessage('Avatar updated successfully!', 'success');
        } else if (urlParams.has('avatar_error')) {
            showMessage(urlParams.get('avatar_error'), 'error');
        }
        
        // Check for profile update success message
        if (urlParams.has('profile_success')) {
            showMessage('Profile updated successfully!', 'success');
        } else if (urlParams.has('profile_error')) {
            showMessage(urlParams.get('profile_error'), 'error');
        }
    });
    
    // Change avatar functionality
    const changeAvatarBtn = document.getElementById('changeAvatarBtn');
    const profileAvatar = document.getElementById('profileAvatar');
    const avatarUploadModal = document.getElementById('avatarUploadModal');
    const closeModalBtn = document.querySelector('.close-modal');
    
    if (changeAvatarBtn) {
        changeAvatarBtn.addEventListener('click', () => {
            avatarUploadModal.style.display = 'block';
        });
    }
    
    // Also allow clicking on the avatar to change it
    if (profileAvatar) {
        profileAvatar.addEventListener('click', () => {
            avatarUploadModal.style.display = 'block';
        });
    }
    
    // Close modal with animation
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            const modalContent = avatarUploadModal.querySelector('.modal-content');
            modalContent.classList.add('modal-close-animation');
            
            setTimeout(() => {
                avatarUploadModal.style.display = 'none';
                modalContent.classList.remove('modal-close-animation');
            }, 300);
        });
    }
    
    // Close modal when clicking outside
    if (avatarUploadModal) {
        avatarUploadModal.addEventListener('click', (e) => {
            if (e.target === avatarUploadModal) {
                const modalContent = avatarUploadModal.querySelector('.modal-content');
                modalContent.classList.add('modal-close-animation');
                
                setTimeout(() => {
                    avatarUploadModal.style.display = 'none';
                    modalContent.classList.remove('modal-close-animation');
                }, 300);
            }
        });
    }
    
    // Handle avatar form submission
    const avatarForm = document.querySelector('.avatar-upload-form');
    if (avatarForm) {
        avatarForm.addEventListener('submit', (e) => {
            const fileInput = document.getElementById('avatar');
            const file = fileInput.files[0];
            
            if (!file) {
                e.preventDefault();
                showMessage('Please select a file to upload.', 'error');
                return;
            }
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                showMessage('Invalid file type. Only JPG, PNG, and GIF files are allowed.', 'error');
                return;
            }
            
            // Validate file size (5MB limit)
            if (file.size > 5 * 1024 * 1024) {
                e.preventDefault();
                showMessage('File size exceeds 5MB limit.', 'error');
                return;
            }
            
            // Show uploading message
            showMessage('Uploading avatar...', 'info');
        });
    }
    
    // Settings toggle switches
    const toggleSwitches = document.querySelectorAll('.switch input');
    toggleSwitches.forEach(switchElem => {
        switchElem.addEventListener('change', function() {
            const settingName = this.closest('.setting-option').querySelector('span').textContent;
            if (this.checked) {
                showMessage(`${settingName} enabled`, 'success');
            } else {
                showMessage(`${settingName} disabled`, 'info');
            }
        });
    });
    
    // Action buttons
    const actionButtons = document.querySelectorAll('.action-buttons .btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const buttonText = this.textContent.trim();
            if (buttonText.includes('Download')) {
                showMessage('Downloading report...', 'info');
                // Simulate download
                setTimeout(() => {
                    showMessage('Report downloaded successfully!', 'success');
                }, 1000);
            } else if (buttonText.includes('Contact')) {
                showMessage('Opening messaging interface...', 'info');
                // Simulate opening contact interface
                setTimeout(() => {
                    showMessage('Messaging interface opened', 'success');
                }, 500);
            }
        });
    });
});

// Show message function
function showMessage(message, type) {
    // Remove any existing messages
    const existingMessage = document.querySelector('.message-popup');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create message element
    const messageEl = document.createElement('div');
    messageEl.className = `message-popup ${type}`;
    messageEl.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'info' ? 'info-circle' : 'exclamation-triangle'}"></i>
        ${message}
    `;
    
    // Add to document
    document.body.appendChild(messageEl);
    
    // Remove after delay
    setTimeout(() => {
        messageEl.remove();
    }, 3000);
}

// Set storage data function
function setStorageData(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
}

// Get storage data function
function getStorageData(key) {
    const data = localStorage.getItem(key);
    return data ? JSON.parse(data) : null;
}

// Animate elements in a section with staggered 3D effects
function animateSectionElements(section) {
    const elements = section.querySelectorAll('.form-group, .settings-section, .avatar-upload-section, .child-section, .widget');
    elements.forEach((el, index) => {
        // Add slight delay for staggered effect
        setTimeout(() => {
            el.style.animation = 'sectionElementEntrance 0.6s cubic-bezier(0.25, 0.8, 0.25, 1) forwards';
        }, index * 100);
    });
}

// Animate child section elements with staggered 3D effects
function animateChildSectionElements(content, index) {
    const elements = content.querySelectorAll('.summary-card, .subject-performance, .action-buttons');
    elements.forEach((el, elIndex) => {
        // Add delay based on both section index and element index
        setTimeout(() => {
            el.style.animation = 'childElementEntrance 0.5s cubic-bezier(0.25, 0.8, 0.25, 1) forwards';
        }, elIndex * 150 + index * 100);
    });
}