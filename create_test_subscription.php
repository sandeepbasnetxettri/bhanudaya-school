<?php
require_once 'config/dbconnection.php';

echo "<h2>Create Test Push Subscription</h2>\n";

try {
    // Check if we have any users
    $stmt = $pdo->query("SELECT id, email, role FROM users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p>Found user: " . htmlspecialchars($user['email']) . " (ID: " . $user['id'] . ", Role: " . $user['role'] . ")</p>\n";
        
        // Check if this user already has a subscription
        $stmt = $pdo->prepare("SELECT id FROM push_subscriptions WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $existingSubscription = $stmt->fetch();
        
        if ($existingSubscription) {
            echo "<p>User already has a subscription (ID: " . $existingSubscription['id'] . ")</p>\n";
        } else {
            // Create a test subscription
            $stmt = $pdo->prepare("INSERT INTO push_subscriptions (user_id, endpoint, p256dh, auth) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([
                $user['id'],
                'https://example.com/test-endpoint-' . time(),
                'test-p256dh-key-' . time(),
                'test-auth-key-' . time()
            ]);
            
            if ($result) {
                echo "<p class='success'>✓ Created test subscription for user ID " . $user['id'] . "</p>\n";
                echo "<p>Now you can test sending push notifications!</p>\n";
            } else {
                echo "<p class='error'>✗ Failed to create test subscription</p>\n";
            }
        }
    } else {
        echo "<p class='warning'>⚠ No users found in the database</p>\n";
        echo "<p>Please create a user account first.</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<p><a href='check_db_web.php'>Check Database Again</a> | <a href='auth/php/admin-dashboard.php'>Back to Admin Dashboard</a></p>\n";
?>