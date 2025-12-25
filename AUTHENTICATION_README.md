# Authentication System Setup

This document explains how to set up and use the authentication system for the Excellence School website.

## Folder Structure

```
/auth/
  ├── /html/              # HTML files
  │   ├── login.html         # Login form (static)
  │   ├── register.html      # Registration form (static)
  │   ├── profile.html       # User profile page
  │   ├── student-portal.html # Student dashboard
  │   ├── admin-dashboard.html # Admin dashboard
  │   └── logout.html        # Logout page
  ├── /css/               # CSS files
  │   ├── auth.css           # Authentication page styles
  │   ├── portal.css         # Student portal styles
  │   ├── profile.css        # Profile page styles
  │   └── admin.css          # Admin dashboard styles
  ├── /js/                # JavaScript files
  │   ├── login.js           # Login page functionality
  │   ├── signup.js          # Registration page functionality
  │   ├── profile.js         # Profile page functionality
  │   ├── student-portal.js   # Student portal functionality
  │   └── admin.js           # Admin dashboard functionality
  └── /php/               # PHP files
      ├── login.php          # Login handler
      ├── register.php       # Registration handler
      ├── logout.php         # Logout handler
      ├── profile.php        # User profile handler
      ├── student-portal.php # Student dashboard
      └── admin-dashboard.php # Admin dashboard

/config/
  └── dbconnection.php   # Database connection file
```
/css/
  ├── auth.css           # Authentication page styles
  ├── portal.css         # Student portal styles
  └── admin.css          # Admin dashboard styles
```

## Database Setup

1. Create a MySQL database named `school_management`
2. Import the database schema from `school_management.sql`
3. Update the database credentials in `/config/dbconnection.php`:

```php
$servername = "localhost";
$username = "your_database_username";
$password = "your_database_password";
$dbname = "school_management";
```

## User Roles

The system supports the following user roles:
- **admin**: Full access to admin dashboard
- **teacher**: Access to teacher-specific features
- **student**: Access to student portal
- **parent**: Access to parent-specific features

## Default Admin Account

To create an admin account, manually insert a record into the `users` table:

```sql
INSERT INTO users (email, full_name, password_hash, role) 
VALUES ('admin@example.com', 'Administrator', '$2y$10$example_hash', 'admin');
```

Note: Use PHP's `password_hash()` function to generate secure password hashes.

## File Access

- Public login page: `/auth/php/login.php`
- Registration page: `/auth/php/register.php`
- Student portal: `/auth/php/student-portal.php`
- Admin dashboard: `/auth/php/admin-dashboard.php`

## Session Management

The system uses PHP sessions to manage user authentication. Sessions are automatically destroyed when users log out.

## Security Features

- Passwords are hashed using PHP's `password_hash()` function
- SQL injection prevention using prepared statements
- XSS prevention using `htmlspecialchars()`
- Session-based authentication
- Role-based access control

## Customization

To customize the authentication system:
1. Modify the CSS files in `/auth/css/` to change the appearance
2. Update the HTML forms in `/auth/html/login.html` and `/auth/html/register.html`
3. Adjust the PHP logic in the handler files in `/auth/php/` as needed
4. Modify the dashboard layouts in `/auth/php/student-portal.php` and `/auth/php/admin-dashboard.php`