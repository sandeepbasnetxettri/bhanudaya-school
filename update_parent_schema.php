<?php
// Script to update the database schema for parent portal

include_once 'config/dbconnection.php';

try {
    // Add relationship column to user_profiles table
    $sql = "ALTER TABLE user_profiles ADD COLUMN relationship ENUM('father', 'mother', 'guardian')";
    $pdo->exec($sql);
    echo "Added relationship column to user_profiles table<br>";
    
    // Add parent_id column to students table
    $sql = "ALTER TABLE students ADD COLUMN parent_id INT, ADD FOREIGN KEY (parent_id) REFERENCES users(id)";
    $pdo->exec($sql);
    echo "Added parent_id column to students table<br>";
    
    echo "Database schema updated successfully!";
} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage();
}
?>
