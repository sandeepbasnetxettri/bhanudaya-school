<?php
require_once 'config/dbconnection.php';

echo "<h2>Database Check for Push Notifications</h2>\n";

try {
    // Check if push_subscriptions table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'push_subscriptions'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "<p>✓ Push subscriptions table exists</p>\n";
        
        // Count total subscriptions
        $stmt = $pdo->query("SELECT COUNT(*) FROM push_subscriptions");
        $count = $stmt->fetchColumn();
        echo "<p>Total push subscriptions: " . $count . "</p>\n";
        
        if ($count > 0) {
            // Show subscriptions by role
            $stmt = $pdo->query("SELECT u.role, COUNT(*) as count FROM push_subscriptions ps JOIN users u ON ps.user_id = u.id GROUP BY u.role");
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<p>Subscriptions by role:</p>\n<ul>\n";
            foreach ($roles as $role) {
                echo "<li>" . htmlspecialchars($role['role']) . ": " . $role['count'] . "</li>\n";
            }
            echo "</ul>\n";
        } else {
            echo "<p>⚠ No push subscriptions found in the database</p>\n";
            echo "<p>This is the most likely cause of the 'Failed to send notification' error.</p>\n";
        }
    } else {
        echo "<p>✗ Push subscriptions table does not exist</p>\n";
        echo "<p>You may need to run the database schema updates.</p>\n";
    }
    
    // Check users table
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "<p>Total users: " . $userCount . "</p>\n";
    
    // Check users by role
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $userRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Users by role:</p>\n<ul>\n";
    foreach ($userRoles as $role) {
        echo "<li>" . htmlspecialchars($role['role']) . ": " . $role['count'] . "</li>\n";
    }
    echo "</ul>\n";
    
} catch (Exception $e) {
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<p><a href='auth/php/admin-dashboard.php'>Back to Admin Dashboard</a></p>\n";
?>