# Environment Configuration

## Overview

This project uses a `.env` file to manage environment-specific configuration variables. This approach allows you to keep sensitive information like database credentials and API keys out of your source code.

## Setup Instructions

### 1. Create the .env File

If you haven't already, create a `.env` file in the root directory of your project with the following content:

```
# Database Configuration
DB_HOST=localhost
DB_NAME=school_management
DB_USER=root
DB_PASS=

# Application Settings
APP_NAME="Excellence School Management System"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/Bhanudayamodelschool

# Security Settings
PASSWORD_HASH_ALGORITHM=2y
PASSWORD_HASH_COST=10

# Session Settings
SESSION_NAME=excellence_school_session
SESSION_LIFETIME=7200

# File Upload Settings
UPLOAD_PATH=uploads/
MAX_FILE_SIZE=5242880
ALLOWED_FILE_TYPES=jpg,jpeg,png,pdf,doc,docx

# Email Settings (if implemented)
EMAIL_HOST=smtp.gmail.com
EMAIL_PORT=587
EMAIL_USERNAME=
EMAIL_PASSWORD=
EMAIL_FROM_ADDRESS=bhanudayahss071@gmail.com
EMAIL_FROM_NAME="Bhanudaya Secondary School"

# Push Notification Settings (if implemented)
VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=
VAPID_EMAIL=bhanudayahss071@gmail.com
```

### 2. Customize Your Environment Variables

Update the values in your `.env` file according to your local setup:

- `DB_HOST`: Your database host (usually `localhost`)
- `DB_NAME`: Your database name (default is `school_management`)
- `DB_USER`: Your database username (default is `root`)
- `DB_PASS`: Your database password (default is empty)
- `APP_URL`: The URL where your application is accessible

### 3. Security Notes

- Never commit your `.env` file to version control
- The `.env` file is already included in the `.gitignore` file to prevent accidental commits
- Always use strong passwords for database access
- Consider using different environment values for development, testing, and production

## How It Works

The environment variables are loaded using the `config/env.php` file, which:

1. Reads the `.env` file from the project root
2. Parses key-value pairs from the file
3. Sets them as environment variables accessible via `$_ENV` and `getenv()`
4. Provides fallback values if the `.env` file is missing or incomplete

## Database Configuration

The database connection file (`config/dbconnection.php`) now uses the environment variables to establish a connection to your database. This allows you to change database settings without modifying the code directly.

## Troubleshooting

### Common Issues

1. **Database Connection Error**: Ensure your database server is running and the credentials in your `.env` file are correct.

2. **Variable Not Found**: If you see errors about undefined environment variables, ensure your `.env` file is in the correct location and properly formatted.

3. **Permissions Error**: Make sure your web server has read access to the `.env` file.

### Verifying Your Setup

To verify that environment variables are loaded correctly, you can create a temporary PHP file to check their values:

```php
<?php
require_once 'config/env.php';
echo "DB Host: " . getenv('DB_HOST') . "\n";
echo "DB Name: " . getenv('DB_NAME') . "\n";
echo "App Name: " . getenv('APP_NAME') . "\n";
?>
```

Remember to delete this file after testing for security reasons.