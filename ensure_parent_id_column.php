<?php
// Script to ensure parent_id column exists in students table
include_once 'config/dbconnection.php';

echo "<h2>Ensuring Parent-Student Linking Database Structure</h2>";

try {
    // Check if parent_id column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM students LIKE 'parent_id'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        // Add parent_id column if it doesn't exist
        echo "<p>Adding parent_id column to students table...</p>";
        $sql = "ALTER TABLE students ADD COLUMN parent_id INT, ADD INDEX(parent_id), ADD FOREIGN KEY (parent_id) REFERENCES users(id)";
        $pdo->exec($sql);
        echo "<p style='color: green;'>SUCCESS: parent_id column added to students table</p>";
    } else {
        echo "<p style='color: green;'>parent_id column already exists in students table</p>";
    }
    
    // Check if parent_guardian_name column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM students LIKE 'parent_guardian_name'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if (!$result) {
        // Add parent_guardian_name column if it doesn't exist
        echo "<p>Adding parent_guardian_name column to students table...</p>";
        $sql = "ALTER TABLE students ADD COLUMN parent_guardian_name VARCHAR(255)";
        $pdo->exec($sql);
        echo "<p style='color: green;'>SUCCESS: parent_guardian_name column added to students table</p>";
    } else {
        echo "<p style='color: green;'>parent_guardian_name column already exists in students table</p>";
    }
    
    echo "<p style='color: blue;'><strong>All required database structures are in place.</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database permissions and try again.</p>";
}

echo "<hr>";
echo "<p><a href='test_parent_student_link.php'>Run Tests</a> | <a href='auth/php/register.php'>Go to Registration</a></p>";
?>