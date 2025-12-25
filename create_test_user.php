<?php
// Create a test user
require_once 'config/dbconnection.php';

try {
    // Hash a test password
    $passwordHash = password_hash('testpass123', PASSWORD_DEFAULT);
    
    // Insert a test user
    $stmt = $pdo->prepare("INSERT INTO users (email, full_name, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['test@example.com', 'Test User', $passwordHash, 'admin']);
    
    echo "Test user created successfully!<br>";
    echo "Email: test@example.com<br>";
    echo "Password: testpass123<br>";
    echo "Role: admin<br>";
    
    // Also create a profile entry
    $userId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id) VALUES (?)");
    $stmt->execute([$userId]);
    
    echo "User profile created successfully!<br>";
} catch (PDOException $e) {
    echo "Error creating test user: " . $e->getMessage();
}
?>