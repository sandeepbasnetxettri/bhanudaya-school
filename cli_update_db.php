<?php
// Command line script to add missing columns to user_profiles table
// Run this script from command line with: php cli_update_db.php

echo "Updating Database Schema...\n";

// Database configuration - same as in config/dbconnection.php
$servername = "localhost";
$username = "root";  // Change this to your database username
$password = "";      // Change this to your database password
$dbname = "school_management";  // Change this to your database name

try {
    // Create connection using PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Add email_notifications column
    try {
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

    // Add sms_alerts column
    try {
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

    // Add push_notifications column
    try {
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

    // Add phone column
    try {
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
    
    echo "✅ Database update completed.\n";
    echo "You can now use the profile functionality without errors.\n";

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>