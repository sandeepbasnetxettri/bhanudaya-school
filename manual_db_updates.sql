-- Manual SQL commands to update the database schema for parent portal

-- Add relationship column to user_profiles table (if it doesn't exist)
ALTER TABLE user_profiles ADD COLUMN relationship ENUM('father', 'mother', 'guardian');

-- Add parent_id column to students table (if it doesn't exist)
ALTER TABLE students ADD COLUMN parent_id INT, ADD INDEX(parent_id), ADD FOREIGN KEY (parent_id) REFERENCES users(id);

-- If the above commands fail because the columns already exist, you can skip them
-- The system will check for column existence before trying to add them