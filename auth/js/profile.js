// Profile Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Handle avatar preview
    const avatarInput = document.getElementById('avatar');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                const fileType = file.type;
                
                if (!allowedTypes.includes(fileType)) {
                    alert('Invalid file type. Only JPG, PNG, and GIF files are allowed.');
                    avatarInput.value = '';
                    return;
                }
                
                // Validate file size (max 5MB)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File size exceeds 5MB limit. Please choose a smaller file.');
                    avatarInput.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create or update preview image
                    let previewImg = document.getElementById('avatarPreview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'avatarPreview';
                        previewImg.style.maxWidth = '200px';
                        previewImg.style.maxHeight = '200px';
                        previewImg.style.borderRadius = '10px';
                        previewImg.style.marginTop = '15px';
                        previewImg.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                        avatarInput.parentNode.insertBefore(previewImg, avatarInput.nextSibling);
                    }
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Handle modal close when clicking outside
    const modal = document.getElementById('avatarUploadModal');
    if (modal) {
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
                // Clear preview when closing modal
                const previewImg = document.getElementById('avatarPreview');
                if (previewImg) {
                    previewImg.remove();
                }
                // Clear file input
                const avatarInput = document.getElementById('avatar');
                if (avatarInput) {
                    avatarInput.value = '';
                }
            }
        });
    }
    
    // Handle profile form submission
    const profileForm = document.querySelector('form[method="post"][name!="upload_avatar"]');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            // Add any client-side validation here if needed
            console.log('Profile form submitted');
        });
    }
    
    // Add drag and drop functionality for avatar upload
    const modalContent = document.querySelector('#avatarUploadModal .modal-content');
    if (modalContent) {
        const fileInput = document.getElementById('avatar');
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            modalContent.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });
        
        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            modalContent.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            modalContent.addEventListener(eventName, unhighlight, false);
        });
        
        // Handle dropped files
        modalContent.addEventListener('drop', handleDrop, false);
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        function highlight() {
            modalContent.style.borderColor = '#4CAF50';
            modalContent.style.boxShadow = '0 0 0 3px rgba(76, 175, 80, 0.3)';
        }
        
        function unhighlight() {
            modalContent.style.borderColor = '';
            modalContent.style.boxShadow = '';
        }
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length) {
                // Trigger file input change event with dropped file
                const file = files[0];
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                
                // Dispatch change event
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        }
    }
});