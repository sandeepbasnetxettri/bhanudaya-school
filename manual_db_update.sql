-- SQL script to add missing columns to user_profiles table

-- Run these commands in your MySQL database management tool (like phpMyAdmin)

ALTER TABLE user_profiles ADD COLUMN email_notifications BOOLEAN DEFAULT TRUE;
ALTER TABLE user_profiles ADD COLUMN sms_alerts BOOLEAN DEFAULT FALSE;
ALTER TABLE user_profiles ADD COLUMN push_notifications BOOLEAN DEFAULT FALSE;
ALTER TABLE user_profiles ADD COLUMN phone VARCHAR(20);

-- If the above commands give "Duplicate column" errors, it means the columns already exist
-- In that case, you can skip this script as your database is already updated