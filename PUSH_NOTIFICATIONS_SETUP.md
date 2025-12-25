# Push Notifications Setup Guide

This guide explains how to set up and configure push notifications for the school management system.

## Overview

Push notifications allow the school management system to send real-time notifications to users' devices even when they're not actively using the application. This feature enhances user engagement and ensures important updates are delivered promptly.

## Prerequisites

1. HTTPS-enabled web server (required for push notifications)
2. Valid SSL certificate
3. VAPID keys for authentication
4. Modern web browser that supports Push API and Notifications API

## Setup Steps

### 1. Database Setup

Run the following SQL scripts to create the necessary tables:

1. `push_notifications_schema.sql` - Creates all push notification related tables
2. `update_user_profiles_table.sql` - Adds notification preference columns to user_profiles table

### 2. Generate VAPID Keys

VAPID (Voluntary Application Server Identification) keys are required for authenticating your application with push services.

You can generate VAPID keys using online tools or libraries. The public key needs to be added to the JavaScript code.

### 3. Configure JavaScript

Update the `js/push-notifications.js` file with your VAPID public key:

```javascript
const vapidPublicKey = 'YOUR_ACTUAL_VAPID_PUBLIC_KEY_HERE';
```

### 4. Server Configuration

Ensure your web server is configured to serve the service worker properly:

1. The service worker file (`js/service-worker.js`) must be served from the root path
2. HTTPS must be enabled
3. CORS headers must be properly configured

### 5. Update Application Files

The following files have been modified to support push notifications:

1. `auth/php/profile.php` - Added notification preference controls
2. `config/dbconnection.php` - Database connection (unchanged but used)
3. `api/save-push-subscription.php` - Saves user push subscriptions
4. `api/remove-push-subscription.php` - Removes user push subscriptions
5. `api/send-push-notification.php` - Sends push notifications to users

## How It Works

### User Flow

1. User enables push notifications in their profile settings
2. Browser requests permission to send notifications
3. If granted, the browser subscribes the user to push notifications
4. Subscription details are sent to the server and stored in the database
5. When the system needs to send a notification, it retrieves subscriptions and sends push messages

### Technical Implementation

1. **Service Worker**: Handles receiving and displaying push notifications
2. **JavaScript Library**: Manages subscription/unsubscription process
3. **API Endpoints**: Handle subscription storage and notification sending
4. **Database Storage**: Stores user preferences and subscription information

## Testing

To test push notifications:

1. Ensure you're accessing the site over HTTPS
2. Navigate to your profile settings
3. Enable push notifications
4. Grant browser permission when prompted
5. Trigger a notification through the admin panel or by calling the API directly

## Troubleshooting

### Common Issues

1. **Push notifications not working**: Ensure HTTPS is enabled
2. **Permission denied**: Check browser settings and try clearing site data
3. **Subscription failing**: Verify VAPID keys are correctly configured
4. **Notifications not displaying**: Check service worker registration in browser dev tools

### Debugging Tips

1. Check browser console for JavaScript errors
2. Verify service worker is registered and activated
3. Confirm push subscription is being saved to the database
4. Check server logs for API errors

## Security Considerations

1. Always use HTTPS in production
2. Store VAPID private key securely
3. Validate all API inputs
4. Implement proper authentication for notification sending endpoints
5. Regularly rotate VAPID keys

## Customization

You can customize the notification behavior by modifying:

1. `js/service-worker.js` - Notification display and click handling
2. `api/send-push-notification.php` - Notification content and targeting
3. `js/push-notifications.js` - Subscription process and error handling

## Limitations

1. Push notifications only work in browsers that support the Push API
2. Users must grant permission to receive notifications
3. Mobile browsers may have different behavior than desktop browsers
4. Some browsers may limit the number of notifications that can be sent

## Future Enhancements

Consider implementing:

1. Notification categories and filtering
2. Rich notifications with images and actions
3. Scheduled notifications
4. Analytics for notification engagement
5. Integration with mobile app platforms (Firebase, APNs, etc.)