# Push Notifications Implementation

## Overview

This document describes the complete implementation of push notifications for the school management system. Push notifications allow the system to send real-time alerts to users even when they're not actively using the application.

## Architecture

The push notification system consists of the following components:

1. **Frontend Components**
   - Service worker for handling push events
   - JavaScript library for subscription management
   - UI controls in user profile for preference management

2. **Backend Components**
   - Database tables for storing subscriptions and preferences
   - API endpoints for subscription management
   - Notification sending functionality

3. **Integration Points**
   - User profile settings
   - Admin dashboard
   - Notice creation workflow

## File Structure

```
school_management/
├── api/
│   ├── save-push-subscription.php
│   ├── remove-push-subscription.php
│   ├── send-push-notification.php
│   ├── notify-new-notice.php
│   ├── create-notice.php
│   └── send-test-notification.php
├── auth/
│   ├── php/
│   │   ├── profile.php (modified)
│   │   ├── admin-dashboard.php (modified)
│   │   └── components/
│   │       └── push-notification-form.php
│   └── css/
│       └── admin.css (updated)
├── js/
│   ├── push-notifications.js
│   └── service-worker.js
├── config/
│   └── dbconnection.php (used)
└── sql/
    ├── push_notifications_schema.sql
    └── update_user_profiles_table.sql
```

## Implementation Details

### 1. Database Schema

The implementation adds three new tables:

1. **user_notification_preferences** - Stores user notification preferences
2. **push_subscriptions** - Stores push subscription details
3. **notifications** - Stores notification messages

Additionally, the `user_profiles` table is updated with three new boolean columns:
- `email_notifications`
- `sms_alerts`
- `push_notifications`

### 2. Frontend Implementation

#### Service Worker
The service worker (`js/service-worker.js`) handles:
- Push notification reception
- Notification display
- Click handling

#### JavaScript Library
The JavaScript library (`js/push-notifications.js`) manages:
- Subscription/unsubscription process
- Permission requests
- Communication with backend APIs

#### User Interface
The profile page (`auth/php/profile.php`) now includes:
- Toggle switches for notification preferences
- JavaScript handlers for push notification toggle
- Form submission for saving preferences

### 3. Backend Implementation

#### API Endpoints

1. **save-push-subscription.php** - Saves user push subscriptions to database
2. **remove-push-subscription.php** - Removes user push subscriptions from database
3. **send-push-notification.php** - Core notification sending functionality
4. **notify-new-notice.php** - Notification sending for new notices
5. **create-notice.php** - Notice creation with automatic notification sending
6. **send-test-notification.php** - Test interface for sending notifications

#### Database Integration
All database operations use prepared statements to prevent SQL injection.

### 4. Admin Integration

The admin dashboard now includes:
- A push notification form component
- Ability to send test notifications to users
- Integration with existing notice management

## Usage

### For Users
1. Navigate to profile settings
2. Enable push notifications toggle
3. Grant browser permission when prompted
4. Receive notifications for school announcements

### For Administrators
1. Access the admin dashboard
2. Use the "Send Push Notification" card
3. Compose and send notifications to users

### For Developers
1. Integrate `notifyNewNotice()` function when creating new notices
2. Use `sendPushNotificationToUser()` or `sendPushNotificationToAllUsers()` for custom notifications
3. Extend functionality as needed

## Security Considerations

1. All API endpoints validate user authentication
2. Admin-only endpoints check for proper roles
3. Database queries use prepared statements
4. HTTPS is required for push notifications to work
5. VAPID keys should be kept secure

## Testing

To test the push notification functionality:

1. Ensure the application is served over HTTPS
2. Update the VAPID public key in `js/push-notifications.js`
3. Run the SQL scripts to create database tables
4. Navigate to profile settings and enable push notifications
5. Use the admin dashboard to send a test notification

## Limitations

1. Push notifications require HTTPS
2. Browser support varies
3. Mobile browsers may have different behavior
4. Users must grant permission
5. Requires valid VAPID keys for production use

## Future Enhancements

1. Notification categories and filtering
2. Rich notifications with images
3. Scheduled notifications
4. Analytics and reporting
5. Mobile app integration
6. Notification templates
7. User segmentation
8. Delivery confirmation

## Troubleshooting

Common issues and solutions:

1. **Notifications not appearing**
   - Check HTTPS is enabled
   - Verify service worker registration
   - Confirm user granted permission

2. **Subscription failures**
   - Check VAPID keys are correctly configured
   - Verify database connectivity
   - Check browser console for errors

3. **Database errors**
   - Ensure SQL scripts have been run
   - Check database credentials
   - Verify table permissions

## Conclusion

This implementation provides a complete push notification system that integrates with the existing school management system. Users can control their notification preferences, and administrators can easily send important announcements to keep the school community informed.