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
    <title>Push Notification Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; }
        .warning { color: orange; }
        .error { color: red; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .back-link { display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
<div class='container'>
<h2>Push Notification System Diagnostics</h2>";

try {
    // Check if required tables exist
    echo "<h3>Database Schema Check</h3>";
    
    $tables = ['users', 'push_subscriptions'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->fetch()) {
            echo "<p class='success'>✓ Table '$table' exists</p>";
        } else {
            echo "<p class='error'>✗ Table '$table' does not exist</p>";
        }
    }
    
    // Count total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "<h3>User Statistics</h3>";
    echo "<p>Total users: <strong>$userCount</strong></p>";
    
    // Users by role
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role ORDER BY role");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table>
            <tr><th>Role</th><th>Count</th></tr>";
    foreach ($roles as $role) {
        echo "<tr><td>{$role['role']}</td><td>{$role['count']}</td></tr>";
    }
    echo "</table>";
    
    // Count total subscriptions
    $stmt = $pdo->query("SELECT COUNT(*) FROM push_subscriptions");
    $subscriptionCount = $stmt->fetchColumn();
    echo "<h3>Push Subscription Statistics</h3>";
    echo "<p>Total subscriptions: <strong>$subscriptionCount</strong></p>";
    
    if ($subscriptionCount > 0) {
        // Subscriptions by role
        $stmt = $pdo->query("SELECT u.role, COUNT(*) as count FROM push_subscriptions ps JOIN users u ON ps.user_id = u.id GROUP BY u.role ORDER BY u.role");
        $subRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table>
                <tr><th>Role</th><th>Subscriptions</th></tr>";
        foreach ($subRoles as $role) {
            echo "<tr><td>{$role['role']}</td><td>{$role['count']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠ No push subscriptions found. This explains the error you're seeing.</p>";
    }
    
    // Check if there are students specifically
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
    $studentCount = $stmt->fetchColumn();
    echo "<h3>Student-Specific Information</h3>";
    echo "<p>Student users: <strong>$studentCount</strong></p>";
    
    if ($studentCount > 0) {
        $stmt = $pdo->query("SELECT u.id, u.email, u.full_name FROM users u WHERE u.role = 'student' LIMIT 5");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<p>Sample student users:</p>";
        echo "<table>
                <tr><th>ID</th><th>Name</th><th>Email</th></tr>";
        foreach ($students as $student) {
            echo "<tr><td>{$student['id']}</td><td>{$student['full_name']}</td><td>{$student['email']}</td></tr>";
        }
        echo "</table>";
        
        // Check how many students have subscriptions
        $stmt = $pdo->query("SELECT COUNT(*) FROM push_subscriptions ps JOIN users u ON ps.user_id = u.id WHERE u.role = 'student'");
        $studentSubCount = $stmt->fetchColumn();
        echo "<p>Students with push subscriptions: <strong>$studentSubCount</strong></p>";
        
        if ($studentSubCount == 0) {
            echo "<p class='warning'>⚠ None of your student users have subscribed to push notifications yet.</p>";
            echo "<h3>Possible Solutions:</h3>";
            echo "<ol>
                    <li>Students need to visit the website and subscribe to push notifications through their browsers</li>
                    <li>Create test subscriptions for development/testing purposes</li>
                  </ol>";
        }
    } else {
        echo "<p class='warning'>⚠ No student users found in the system</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<a href='auth/php/admin-dashboard.php' class='back-link'>Back to Admin Dashboard</a>";
echo "</div></body></html>";
?>