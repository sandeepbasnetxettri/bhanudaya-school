<?php
// Test database connection
require_once 'config/dbconnection.php';

try {
    // Test the connection by querying the database
    $stmt = $pdo->query("SELECT VERSION() as version");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Database connection successful!<br>";
    echo "MySQL Version: " . $row['version'];
    
    // Test if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<br>Users table exists!";
    } else {
        echo "<br>Users table does not exist!";
    }
    
    // Test if user_profiles table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'user_profiles'");
    if ($stmt->rowCount() > 0) {
        echo "<br>User profiles table exists!";
    } else {
        echo "<br>User profiles table does not exist!";
    }
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>