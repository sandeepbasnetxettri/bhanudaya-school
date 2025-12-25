# Database Update Instructions for Parent Portal

## Issue
The parent portal is showing a "Column not found: 1054 Unknown column 'parent_id' in 'where clause'" error because the database schema hasn't been updated with the required columns.

## Solution Options

### Option 1: Web-based Update Script (Recommended)
1. Access the following URL in your web browser:
   ```
   http://localhost/update_database_web.php
   ```

2. This script will automatically:
   - Check if the required columns exist
   - Add the missing `relationship` column to the `user_profiles` table
   - Add the missing `parent_id` column to the `students` table
   - Display success or error messages

3. After successful execution, you can access the parent portal pages without errors.

### Option 2: Manual SQL Execution
If you have direct access to your MySQL database:

1. Connect to your MySQL database using a tool like phpMyAdmin, MySQL Workbench, or command line.

2. Execute the commands in the `manual_db_updates.sql` file:
   ```sql
   -- Add relationship column to user_profiles table
   ALTER TABLE user_profiles ADD COLUMN relationship ENUM('father', 'mother', 'guardian');
   
   -- Add parent_id column to students table
   ALTER TABLE students ADD COLUMN parent_id INT, ADD INDEX(parent_id), ADD FOREIGN KEY (parent_id) REFERENCES users(id);
   ```

### Option 3: Using MySQL Command Line
1. Open Command Prompt or Terminal
2. Navigate to your MySQL bin directory (if it's not in your PATH)
3. Execute:
   ```bash
   mysql -u your_username -p school_management < manual_db_updates.sql
   ```
   Replace `your_username` with your MySQL username.

## Verification
After applying the database updates:

1. Try accessing the parent portal pages again:
   - Parent Login: `http://localhost/auth/php/parent-login.php`
   - Parent Registration: `http://localhost/auth/php/parent-register.php`

2. You should no longer see the "Unknown column 'parent_id'" error.

## Troubleshooting

### If you get "Duplicate column name" errors:
This means the columns already exist in your database. You can safely ignore these errors.

### If you get permission errors:
Make sure your database user has ALTER permissions on the database.

### If the web script doesn't work:
1. Check that your web server is running
2. Verify that PHP is properly configured
3. Check the database connection settings in `config/dbconnection.php`

## Additional Notes
- The update script checks for column existence before trying to add them
- Foreign key constraints are properly set up
- The changes are backward compatible with existing data