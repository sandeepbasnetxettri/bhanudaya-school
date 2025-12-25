<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: auth/php/login.php');
    exit;
}

$filesToDelete = [
    'diagnose_push_notifications.php',
    'create_test_subscriptions.php',
    'cleanup_diagnostics.php' // This file will delete itself
];

echo "<!DOCTYPE html>
<html>
<head>
    <title>Clean Up Diagnostic Files</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .back-link { display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
<div class='container'>
<h2>Cleaning Up Diagnostic Files</h2>";

foreach ($filesToDelete as $file) {
    $filePath = __DIR__ . '/' . $file;
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo "<p class='success'>✓ Deleted: " . htmlspecialchars($file) . "</p>";
        } else {
            echo "<p class='error'>✗ Failed to delete: " . htmlspecialchars($file) . "</p>";
        }
    } else {
        echo "<p class='info'>ℹ File not found: " . htmlspecialchars($file) . "</p>";
    }
}

echo "<p>All diagnostic files have been cleaned up.</p>";
echo "<a href='auth/php/admin-dashboard.php' class='back-link'>Back to Admin Dashboard</a>";
echo "</div></body></html>";
?>
