-- Add notification preference columns to user_profiles table
ALTER TABLE user_profiles 
ADD COLUMN email_notifications BOOLEAN DEFAULT TRUE,
ADD COLUMN sms_alerts BOOLEAN DEFAULT FALSE,
ADD COLUMN push_notifications BOOLEAN DEFAULT FALSE;