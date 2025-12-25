<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: auth/php/login.php');
    exit;
}

require_once 'config/dbconnection.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Create Test Subscriptions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; }
        .warning { color: orange; }
        .error { color: red; }
        .back-link { display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
        .action-button { display: inline-block; margin: 10px 0; padding: 10px 15px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
    </style>
</head>
<body>
<div class='container'>
<h2>Create Test Push Subscriptions</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_subscriptions'])) {
    try {
        $createdCount = 0;
        
        // Get users who don't have subscriptions yet
        $stmt = $pdo->query("SELECT u.id, u.role FROM users u LEFT JOIN push_subscriptions ps ON u.id = ps.user_id WHERE ps.user_id IS NULL LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($users as $user) {
            // Create a test subscription for each user
            $stmt = $pdo->prepare("INSERT INTO push_subscriptions (user_id, endpoint, p256dh, auth) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([
                $user['id'],
                'https://test-push-service.example.com/endpoint/' . uniqid(),
                'test-p256dh-' . uniqid(),
                'test-auth-' . uniqid()
            ]);
            
            if ($result) {
                $createdCount++;
            }
        }
        
        echo "<p class='success'>✓ Successfully created $createdCount test subscriptions!</p>";
        echo "<p>You can now test sending push notifications to students and other user roles.</p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>Error creating subscriptions: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_subscriptions'])) {
    try {
        // Clear all subscriptions (for testing purposes)
        $stmt = $pdo->query("DELETE FROM push_subscriptions");
        $deletedCount = $stmt->rowCount();
        echo "<p class='success'>✓ Cleared $deletedCount subscriptions from the database.</p>";
    } catch (Exception $e) {
        echo "<p class='error'>Error clearing subscriptions: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Display current statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM push_subscriptions");
    $subscriptionCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT u.role, COUNT(*) as count FROM push_subscriptions ps JOIN users u ON ps.user_id = u.id GROUP BY u.role");
    $subscriptionsByRole = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Current Subscription Status</h3>";
    echo "<p>Total subscriptions: <strong>$subscriptionCount</strong></p>";
    
    if ($subscriptionCount > 0) {
        echo "<table>
                <tr><th>Role</th><th>Subscriptions</th></tr>";
        foreach ($subscriptionsByRole as $row) {
            echo "<tr><td>{$row['role']}</td><td>{$row['count']}</td></tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>Error fetching subscription stats: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h3>Create Test Subscriptions</h3>";
echo "<form method='post'>";
echo "<button type='submit' name='create_subscriptions' class='action-button'>Create Test Subscriptions for Unsubscribed Users</button>";
echo "</form>";

echo "<h3>Clear All Subscriptions (Testing Only)</h3>";
echo "<form method='post' onsubmit='return confirm(\"Are you sure you want to delete all subscriptions?\")'>";
echo "<button type='submit' name='clear_subscriptions' class='action-button' style='background-color: #f44336;'>Clear All Subscriptions</button>";
echo "</form>";

echo "<a href='diagnose_push_notifications.php' class='back-link'>Run Diagnostics</a> | ";
echo "<a href='auth/php/admin-dashboard.php' class='back-link'>Back to Admin Dashboard</a>";
echo "</div></body></html>";
?>