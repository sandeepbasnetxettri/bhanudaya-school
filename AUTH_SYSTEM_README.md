# Authentication System with Profile and Avatar

This is a complete authentication system with user registration, login, profile management, and avatar upload functionality.

## Features

1. **User Registration**
   - Email, full name, password, and role selection
   - Password validation (minimum 6 characters)
   - Email uniqueness check
   - Automatic login after registration

2. **User Login**
   - Email and password authentication
   - Password hashing for security
   - Role-based redirection after login

3. **User Profile**
   - View and update personal information
   - Avatar upload functionality
   - Profile picture display

4. **Security Features**
   - Password hashing using PHP's `password_hash()`
   - PDO prepared statements to prevent SQL injection
   - Session-based authentication
   - XSS prevention with `htmlspecialchars()`

## File Structure

```
/auth/
  ├── /css/
  │   ├── auth.css     # Authentication page styles
  │   └── profile.css  # Profile page styles
  ├── /js/
  │   ├── login.js     # Login page functionality
  │   ├── signup.js    # Registration page functionality
  │   └── profile.js   # Profile page functionality
  └── /php/
      ├── login.php        # Login handler
      ├── register.php     # Registration handler
      ├── profile.php      # Profile management
      ├── logout.php       # Logout handler
      └── (other pages)
```

## Database Schema

The system uses two main tables:

1. **users** - Stores basic user information
   - id (primary key)
   - email (unique)
   - full_name
   - password_hash
   - role (admin, teacher, student, parent)
   - timestamps

2. **user_profiles** - Stores additional profile information
   - user_id (foreign key to users table)
   - phone
   - date_of_birth
   - gender
   - address
   - avatar (filename of uploaded avatar)
   - timestamps

## Setup Instructions

1. **Database Setup**
   - Import `school_management.sql` into your MySQL database
   - Update database credentials in `config/dbconnection.php`

2. **Test User Creation**
   - Run `create_test_user.php` to create a test user
   - Email: test@example.com
   - Password: testpass123
   - Role: admin

3. **Testing**
   - Visit `auth/php/login.php` to test login
   - Visit `auth/php/register.php` to test registration
   - After login, visit `auth/php/profile.php` to test profile management

## Avatar Upload Features

- Supports JPG, PNG, and GIF formats
- Files are stored in `uploads/avatars/` directory
- Filenames are randomized to prevent conflicts
- Avatar is displayed on profile page
- Users can update their avatar anytime

## Security Notes

- All passwords are hashed using PHP's built-in `password_hash()` function
- All database queries use PDO prepared statements to prevent SQL injection
- All user input is sanitized with `htmlspecialchars()` to prevent XSS attacks
- Session variables are properly destroyed on logout

## Customization

To customize the system:
1. Modify CSS files in `/auth/css/` to change appearance
2. Update HTML structure in PHP files as needed
3. Adjust validation rules in PHP files
4. Modify database schema if additional fields are needed