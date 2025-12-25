# PHPMailer Setup Guide

This guide explains how to set up PHPMailer for sending emails from the registration system.

## Prerequisites

1. Composer (PHP dependency manager)
2. PHP 5.5 or higher
3. Gmail account for sending emails

## Installation Steps

### 1. Install Composer (if not already installed)

Download and install Composer from [https://getcomposer.org/](https://getcomposer.org/)

### 2. Install PHPMailer via Composer

Open a terminal/command prompt in the project root directory and run:

```bash
composer require phpmailer/phpmailer
```

This will create a `vendor` directory with PHPMailer files.

### 3. Update Email Configuration

Update the email configuration in `login_system/php/config/email.php`:

```php
<?php
// Email Configuration

// SMTP Configuration for Gmail
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');

// IMPORTANT: Use your actual Gmail credentials
define('SMTP_USERNAME', 'sandeepbasnetxettri@gmail.com');

// Use an App Password, not your regular password
// Generate one at: https://myaccount.google.com/apppasswords
define('SMTP_PASSWORD', 'your_app_password_here');

define('SENDER_EMAIL', 'sandeepbasnetxettri@gmail.com');
define('SENDER_NAME', 'Excellence School');
?>
```

### 4. Enable Gmail App Password

1. Go to your Google Account settings
2. Navigate to Security → 2-Step Verification
3. Enable 2-Step Verification if not already enabled
4. Go to Security → App passwords
5. Generate a new app password for "Mail"
6. Use this app password in place of your regular Gmail password

### 5. Update Email Sender Utility

Replace the contents of `login_system/php/utils/email_sender.php` with:

```php
<?php
// Email Sender Utility with PHPMailer

require_once '../config/email.php';

// Autoload PHPMailer
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send email using PHPMailer with SMTP
 */
function sendEmail($to, $subject, $message, $isHtml = true) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SENDER_EMAIL, SENDER_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML($isHtml);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Send registration confirmation email to student
 */
function sendRegistrationConfirmation($email, $fullName, $studentId = null) {
    // Include the email template functions
    require_once '../config/email.php';
    
    $template = getRegistrationEmailTemplate($fullName, $email, $studentId);
    return sendEmail($email, $template['subject'], $template['message'], true);
}

/**
 * Send notification email to admin
 */
function sendAdminNotification($adminEmail, $fullName, $email, $studentId = null) {
    // Include the email template functions
    require_once '../config/email.php';
    
    $template = getAdminNotificationEmailTemplate($fullName, $email, $studentId);
    return sendEmail($adminEmail, $template['subject'], $template['message'], true);
}
?>
```

## Testing the Setup

After completing the setup:

1. Test the registration form
2. Check that emails are being sent to both the student and admin
3. Verify that the emails arrive in the inbox (check spam/junk folders if not in inbox)

## Troubleshooting

### Common Issues:

1. **Emails not being sent**: 
   - Check Gmail credentials and app password
   - Verify SMTP settings
   - Check server firewall settings

2. **Emails going to spam**:
   - Add proper SPF and DKIM records to your domain
   - Use a professional email signature
   - Avoid spam trigger words

3. **Authentication errors**:
   - Ensure 2-factor authentication is enabled
   - Use app passwords, not regular passwords
   - Check that the Gmail account allows less secure apps (if using regular password)

## Security Considerations

1. Never commit passwords to version control
2. Use environment variables for sensitive data
3. Regularly rotate app passwords
4. Monitor email sending activity for unusual patterns

## Alternative Solutions

If PHPMailer proves difficult to set up, consider:

1. **Using a transactional email service** like SendGrid, Mailgun, or Amazon SES
2. **Configuring a local mail server** like Postfix or Sendmail
3. **Using hosting provider's built-in email functions**

For shared hosting environments, check with your provider for recommended email sending methods.