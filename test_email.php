<?php
// Test Email Functionality

require_once 'login_system/php/config/email.php';
require_once 'login_system/php/utils/email_sender.php';

echo "<h1>Testing Email Functionality</h1>";

// Test sending a simple email
$testEmail = "sandeepbasnetxettri@gmail.com";
$testName = "Sandeep Basnet";
$testStudentId = "STU2025001";

echo "<p>Sending registration confirmation email...</p>";

// Test registration confirmation email
$result1 = sendRegistrationConfirmation($testEmail, $testName, $testStudentId);

if ($result1) {
    echo "<p style='color: green;'>✅ Registration confirmation email sent successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to send registration confirmation email.</p>";
    echo "<p>Check your server's mail configuration. On Windows, you may need to configure SMTP settings in php.ini.</p>";
}

echo "<p>Sending admin notification email...</p>";

// Test admin notification email
$result2 = sendAdminNotification($testEmail, $testName, $testEmail, $testStudentId);

if ($result2) {
    echo "<p style='color: green;'>✅ Admin notification email sent successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to send admin notification email.</p>";
    echo "<p>Check your server's mail configuration. On Windows, you may need to configure SMTP settings in php.ini.</p>";
}

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Check your inbox at {$testEmail} for the test emails</li>";
echo "<li>If emails don't arrive, check your spam/junk folder</li>";
echo "<li>If still having issues, follow the PHPMailer setup guide in PHPMAILER_SETUP.md</li>";
echo "</ol>";

echo "<h2>Server Information:</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Mail Function Available: " . (function_exists('mail') ? 'Yes' : 'No') . "</p>";

// Show current mail configuration if available
if (ini_get('SMTP')) {
    echo "<p>SMTP Server: " . ini_get('SMTP') . "</p>";
    echo "<p>SMTP Port: " . ini_get('smtp_port') . "</p>";
}

?>