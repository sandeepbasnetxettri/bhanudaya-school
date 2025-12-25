<?php
// Clean up test files
$filesToDelete = [
    'check_db.php',
    'check_db.bat',
    'check_db_web.php',
    'create_test_subscription.php',
    'cleanup_test_files.php' // This file will delete itself
];

echo "<h2>Cleaning Up Test Files</h2>\n";

foreach ($filesToDelete as $file) {
    $filePath = __DIR__ . '/' . $file;
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo "<p>✓ Deleted: " . htmlspecialchars($file) . "</p>\n";
        } else {
            echo "<p>✗ Failed to delete: " . htmlspecialchars($file) . "</p>\n";
        }
    } else {
        echo "<p>ℹ File not found: " . htmlspecialchars($file) . "</p>\n";
    }
}

echo "<p>All test files have been cleaned up.</p>\n";
echo "<p><a href='auth/php/admin-dashboard.php'>Back to Admin Dashboard</a></p>\n";
?>
