# Avatar/Profile Image Functionality Implementation

## Overview
This document describes the implementation of avatar/profile image functionality for the Bhanudaya Model School website. Users can now upload profile pictures during registration and update them in their profile settings.

## Database Changes

### Added Avatar Column
- Added `avatar` column to the `user_profiles` table (VARCHAR(500))
- Stores the relative path to the uploaded avatar image
- Supports JPG, JPEG, PNG, and GIF formats

## New Files Created

### 1. profile.php
Located in `/auth/php/profile.php`, this file provides:
- User profile management interface
- Avatar upload functionality
- Profile information editing
- Session-based authentication

### 2. profile.css
Located in `/auth/css/profile.css`, this file provides:
- Styling for profile pages
- Avatar display and upload controls
- Responsive design for all devices

## Modified Files

### 1. register.php
Enhanced with:
- Avatar upload field in the registration form
- File validation and upload processing
- Database storage of avatar path

### 2. student-portal.php
Updated to:
- Display user avatar in the header
- Link to profile page for avatar management

### 3. login.php
Updated to:
- Include profile.css for consistent styling

### 4. school_management.sql
Updated to:
- Add avatar column to user_profiles table

## Implementation Details

### File Upload Process
1. Users can upload avatar images during registration or in profile settings
2. Supported formats: JPG, JPEG, PNG, GIF
3. Maximum file size: 2MB (handled by server defaults)
4. Files are stored in `/uploads/avatars/` directory
5. Filenames are randomized to prevent conflicts
6. Old avatar files are not automatically deleted

### Security Measures
1. File type validation to prevent malicious uploads
2. Unique filenames to prevent overwrites
3. Directory creation with proper permissions
4. Prepared statements to prevent SQL injection
5. XSS protection with htmlspecialchars()

### Display Logic
1. If user has uploaded avatar, display it
2. If no avatar exists, show default user icon
3. Avatar displayed in circular format with border
4. Responsive sizing for different screen sizes

## Usage Instructions

### For Users
1. During registration:
   - Fill in personal information
   - Optionally select a profile picture
   - Submit form to create account

2. Managing profile:
   - Log in to the system
   - Click on username in header to access profile
   - Upload new avatar or update information
   - Save changes

### For Administrators
1. Avatar files are stored in `/uploads/avatars/`
2. Database stores relative paths to avatar files
3. Manual cleanup of unused avatar files may be needed

## Technical Requirements

### Server Configuration
- PHP file upload enabled
- Write permissions for `/uploads/avatars/` directory
- Sufficient disk space for avatar storage

### Browser Support
- Modern browsers supporting HTML5 file upload
- JavaScript enabled for enhanced UX
- CSS3 support for styling

## Future Enhancements

### Planned Improvements
1. Avatar cropping functionality
2. Size optimization for uploaded images
3. Automatic cleanup of unused avatar files
4. Support for additional image formats
5. Integration with social media avatar sources

### Potential Issues
1. Disk space management for large numbers of users
2. Backup considerations for user-uploaded content
3. CDN integration for improved loading times

## Testing Notes

### Verified Functionality
1. Avatar upload during registration
2. Avatar display in user interface
3. Profile updates with avatar changes
4. Error handling for invalid file types
5. Responsive design across device sizes

### Browser Compatibility
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Maintenance

### Regular Tasks
1. Monitor disk space usage in `/uploads/avatars/`
2. Review and clean up unused avatar files
3. Check database for orphaned avatar records

### Troubleshooting
1. If avatars don't display:
   - Check file permissions
   - Verify file paths in database
   - Confirm file existence on server

2. If uploads fail:
   - Check PHP upload limits
   - Verify directory write permissions
   - Review server error logs