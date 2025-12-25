# Email System Documentation

This document explains how the email system works in the student registration system.

## Overview

The email system is designed to send automatic notifications when a new student or admin registers. It consists of:

1. Email configuration file
2. Email templates
3. Email sending utilities
4. Integration with the registration process

## Components

### 1. Configuration (`login_system/php/config/email.php`)

Contains:
- SMTP settings for Gmail
- Sender information
- Email template functions

### 2. Email Templates

Two types of emails are sent:
- **Registration Confirmation**: Sent to the newly registered user
- **Admin Notification**: Sent to the administrator

### 3. Email Utilities (`login_system/php/utils/email_sender.php`)

Provides functions to:
- Send emails using PHP's built-in `mail()` function
- Send registration confirmation emails
- Send admin notification emails

## How It Works

### During Registration

1. User completes registration form
2. System validates input and creates database record
3. If registration is successful:
   - Registration confirmation email is sent to user
   - Admin notification email is sent to administrator
4. Success message is returned to user

### Email Content

#### Registration Confirmation Email
- Welcomes the new user
- Provides account details
- Includes login link

#### Admin Notification Email
- Alerts admin of new registration
- Provides user details
- Includes admin panel link

## Setup Requirements

### For Built-in PHP mail() Function

The system will work with PHP's built-in `mail()` function, but this requires:
- Properly configured mail server on the host
- On Windows, SMTP settings in `php.ini`

### For Gmail SMTP (Recommended)

To use Gmail SMTP:
1. Follow the PHPMailer setup guide in `PHPMAILER_SETUP.md`
2. Enable 2-factor authentication on your Gmail account
3. Generate an App Password
4. Update the configuration with your credentials

## Testing

Run `test_email.php` to verify email functionality:
```
http://localhost/Bhanudayamodelschool/test_email.php
```

## Troubleshooting

### Common Issues

1. **Emails not being sent**
   - Check server mail configuration
   - Verify email addresses are valid
   - Check spam/junk folders

2. **Emails going to spam**
   - Use proper email templates
   - Avoid spam trigger words
   - Set up SPF/DKIM records

3. **Authentication errors**
   - Ensure correct Gmail credentials
   - Use App Passwords, not regular passwords
   - Verify 2-factor authentication is enabled

## Security

1. Email credentials are stored in configuration files
2. Never commit real passwords to version control
3. Use environment variables for production deployments
4. Rotate App Passwords regularly

## Customization

### Modifying Email Templates

Edit the template functions in `login_system/php/config/email.php`:
- `getRegistrationEmailTemplate()`
- `getAdminNotificationEmailTemplate()`

### Changing Email Content

Templates can be customized to include:
- School logo
- Additional user information
- Custom branding
- Links to specific resources

## Future Improvements

1. Add email queue system for better performance
2. Implement email logging for audit trails
3. Add support for attachments
4. Create unsubscribe functionality
5. Add email analytics tracking