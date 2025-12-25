<?php
// Script to add missing columns to user_profiles table
require_once 'config/dbconnection.php';

echo "<h2>Updating Database Schema</h2>";
echo "<pre>";

try {
    // Add email_notifications column
    $sql = "ALTER TABLE user_profiles ADD COLUMN email_notifications BOOLEAN DEFAULT TRUE";
    $pdo->exec($sql);
    echo "✓ Added email_notifications column\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
        echo "✗ Error adding email_notifications column: " . $e->getMessage() . "\n";
    } else {
        echo "ℹ email_notifications column already exists\n";
    }
}

try {
    // Add sms_alerts column
    $sql = "ALTER TABLE user_profiles ADD COLUMN sms_alerts BOOLEAN DEFAULT FALSE";
    $pdo->exec($sql);
    echo "✓ Added sms_alerts column\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
        echo "✗ Error adding sms_alerts column: " . $e->getMessage() . "\n";
    } else {
        echo "ℹ sms_alerts column already exists\n";
    }
}

try {
    // Add push_notifications column
    $sql = "ALTER TABLE user_profiles ADD COLUMN push_notifications BOOLEAN DEFAULT FALSE";
    $pdo->exec($sql);
    echo "✓ Added push_notifications column\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
        echo "✗ Error adding push_notifications column: " . $e->getMessage() . "\n";
    } else {
        echo "ℹ push_notifications column already exists\n";
    }
}

try {
    // Add phone column
    $sql = "ALTER TABLE user_profiles ADD COLUMN phone VARCHAR(20)";
    $pdo->exec($sql);
    echo "✓ Added phone column\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
        echo "✗ Error adding phone column: " . $e->getMessage() . "\n";
    } else {
        echo "ℹ phone column already exists\n";
    }
}

echo "</pre>";
echo "<h3>✅ Database update completed.</h3>";
echo "<p>You can now use the profile functionality without errors.</p>";
?>