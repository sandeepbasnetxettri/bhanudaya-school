<?php
// Web-based script to update the database schema for parent portal

include_once 'config/dbconnection.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Update</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .success { color: green; padding: 15px; background: #dff0d8; border: 1px solid #d6e9c6; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 15px; background: #f2dede; border: 1px solid #ebccd1; border-radius: 5px; margin: 10px 0; }
        h1 { color: #333; }
    </style>
</head>
<body>
<div class='container'>
<h1>Database Schema Update for Parent Portal</h1>";

try {
    // Check if relationship column exists in user_profiles
    $stmt = $pdo->prepare("SHOW COLUMNS FROM user_profiles LIKE 'relationship'");
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        // Add relationship column to user_profiles table
        $sql = "ALTER TABLE user_profiles ADD COLUMN relationship ENUM('father', 'mother', 'guardian')";
        $pdo->exec($sql);
        echo "<div class='success'>Added relationship column to user_profiles table</div>";
    } else {
        echo "<div class='success'>Relationship column already exists in user_profiles table</div>";
    }
    
    // Check if parent_id column exists in students
    $stmt = $pdo->prepare("SHOW COLUMNS FROM students LIKE 'parent_id'");
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        // Add parent_id column to students table
        $sql = "ALTER TABLE students ADD COLUMN parent_id INT, ADD INDEX(parent_id), ADD FOREIGN KEY (parent_id) REFERENCES users(id)";
        $pdo->exec($sql);
        echo "<div class='success'>Added parent_id column to students table</div>";
    } else {
        echo "<div class='success'>Parent_id column already exists in students table</div>";
    }
    
    echo "<div class='success'><strong>All database updates completed successfully!</strong></div>";
    echo "<p>You can now access the parent portal pages without errors.</p>";
    echo "<p><a href='auth/php/parent-login.php'>Go to Parent Login</a></p>";
    
} catch (PDOException $e) {
    echo "<div class='error'>Error updating database schema: " . $e->getMessage() . "</div>";
    echo "<p>Please check your database configuration and permissions.</p>";
}

echo "</div></body></html>";
?>