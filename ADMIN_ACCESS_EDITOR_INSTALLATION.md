# Admin Access Editor Installation Guide

## Overview
The Admin Access Editor is a comprehensive user management system that allows administrators to create, edit, and delete user accounts, as well as manage user roles and permissions.

## Files Created
1. `auth/php/admin-access-editor.php` - Main admin interface for user management
2. `update_push_notifications_tables.php` - Database update script for push notification tables
3. `test_db_tables.php` - Script to verify database tables

## Features
- Create new user accounts with different roles (admin, teacher, student, parent)
- Edit existing user information
- Reset user passwords
- Delete user accounts
- View all users in a sortable table
- Role-based access control

## Installation Steps

### 1. File Installation
The required files have already been created in your project directory:
- The main admin access editor is located at `auth/php/admin-access-editor.php`
- CSS styles have been added to `auth/css/admin.css`

### 2. Database Setup
The system requires three additional tables for full functionality:
1. `user_notification_preferences`
2. `push_subscriptions`
3. `notifications`

To create these tables:

1. Open your browser and navigate to:
   ```
   http://your-domain/update_push_notifications_tables.php
   ```

2. This script will automatically create any missing tables.

Alternatively, you can manually execute the SQL statements from `push_notifications_schema.sql`.

### 3. Verification
To verify that all required tables exist:

1. Open your browser and navigate to:
   ```
   http://your-domain/test_db_tables.php
   ```

2. This script will show which tables exist and which are missing.

## Usage

### Accessing the Admin Access Editor
1. Log in to the admin panel using your admin credentials
2. From the admin dashboard, click on "Access Editor" in the User Management section

### Creating a New User
1. Click the "Create New User" button
2. Fill in the user's full name, email address, password, and select a role
3. Click "Create User"

### Editing a User
1. Find the user in the table
2. Click the "Edit" button in the Actions column
3. Modify the user's information as needed
4. Click "Update User"

### Resetting a User's Password
1. Find the user in the table
2. Click the "Reset Pass" button in the Actions column
3. Enter a new password and confirm it
4. Click "Reset Password"

### Deleting a User
1. Find the user in the table
2. Click the "Delete" button in the Actions column
3. Confirm the deletion in the popup dialog
4. Click "Delete User"

## Security Notes
- Only users with the "admin" role can access the Admin Access Editor
- Passwords are securely hashed using PHP's `password_hash()` function
- The system prevents administrators from deleting their own accounts
- All form inputs are properly sanitized to prevent SQL injection

## Troubleshooting
If you encounter any issues:

1. Ensure all files have been properly copied to their respective directories
2. Verify that the database connection in `config/dbconnection.php` is correct
3. Make sure all required database tables exist
4. Check that the web server has proper read/write permissions for all files

## Support
For additional support or feature requests, please consult the main project documentation or contact the development team.