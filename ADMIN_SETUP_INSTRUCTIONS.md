# Admin User Setup Instructions

## Creating an Admin User

To create an admin user with email "admin@admin.com" and password "admin", follow these steps:

### Method 1: Using the Web Browser (Recommended)

1. Place the file `create_admin_user.php` in your web server directory
2. Navigate to the file in your browser: `http://localhost/Bhanudayamodelschool/create_admin_user.php`
3. The script will create an admin user with the following credentials:
   - Email: `admin@admin.com`
   - Password: `admin`
   - Role: `admin`

### Method 2: Using Command Line (If PHP is available)

Run the following command in your terminal:
```bash
php -f c:/3/htdocs/Bhanudayamodelschool/create_admin_user.php
```

## Logging In

Once the admin user is created, you can log in using these credentials:

1. Go to the login page: `http://localhost/Bhanudayamodelschool/auth/php/login.php`
2. Enter the following credentials:
   - Email: `admin@admin.com`
   - Password: `admin`
3. Click "Login" and you will be redirected to the admin dashboard

## Security Note

For security purposes, after setting up your admin account, consider changing the default password to a more secure one.

## Troubleshooting

If you get an error saying the user already exists, it means the admin user has already been created and you can log in directly with the credentials mentioned above.

If you encounter any database connection errors, make sure your database is running and the configuration in `config/dbconnection.php` is correct.