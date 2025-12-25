<?php
// Script to test if push notification tables exist
require_once 'config/dbconnection.php';

echo "<h2>Checking Database Tables for Push Notifications</h2>";

$tables = ['user_notification_preferences', 'push_subscriptions', 'notifications'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo "<p>✓ Table '$table' exists</p>";
        } else {
            echo "<p>✗ Table '$table' does not exist</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error checking table '$table': " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='index.php'>Back to Home</a></p>";
?>