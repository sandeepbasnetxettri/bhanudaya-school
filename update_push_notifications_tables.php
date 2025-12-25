<?php
// Script to update database with push notification tables
require_once 'config/dbconnection.php';

echo "<h2>Updating Database for Push Notifications</h2>";

try {
    // Check if user_notification_preferences table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'user_notification_preferences'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p>Creating user_notification_preferences table...</p>";
        
        // Create user_notification_preferences table
        $sql = "CREATE TABLE user_notification_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            email_notifications BOOLEAN DEFAULT TRUE,
            sms_alerts BOOLEAN DEFAULT FALSE,
            push_notifications BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_preference (user_id)
        )";
        
        $pdo->exec($sql);
        echo "<p>✓ user_notification_preferences table created successfully</p>";
    } else {
        echo "<p>✓ user_notification_preferences table already exists</p>";
    }
    
    // Check if push_subscriptions table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'push_subscriptions'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p>Creating push_subscriptions table...</p>";
        
        // Create push_subscriptions table
        $sql = "CREATE TABLE push_subscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            endpoint VARCHAR(500) NOT NULL,
            p256dh VARCHAR(100) NOT NULL,
            auth VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_subscription (endpoint)
        )";
        
        $pdo->exec($sql);
        echo "<p>✓ push_subscriptions table created successfully</p>";
    } else {
        echo "<p>✓ push_subscriptions table already exists</p>";
    }
    
    // Check if notifications table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'notifications'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p>Creating notifications table...</p>";
        
        // Create notifications table
        $sql = "CREATE TABLE notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type ENUM('info', 'warning', 'success', 'error') DEFAULT 'info',
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        
        $pdo->exec($sql);
        echo "<p>✓ notifications table created successfully</p>";
    } else {
        echo "<p>✓ notifications table already exists</p>";
    }
    
    echo "<h3>Database update completed successfully!</h3>";
    
} catch (PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>Back to Home</a></p>";
?>