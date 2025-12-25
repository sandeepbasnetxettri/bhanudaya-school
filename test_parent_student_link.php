<?php
// Test script to verify parent-student linking functionality
include_once 'config/dbconnection.php';

echo "<h2>Testing Parent-Student Linking Functionality</h2>";

// Test 1: Check if parent_id column exists in students table
echo "<h3>Test 1: Checking database schema</h3>";
try {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM students LIKE 'parent_id'");
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result) {
        echo "<p style='color: green;'>PASS: parent_id column exists in students table</p>";
    } else {
        echo "<p style='color: red;'>FAIL: parent_id column does not exist in students table</p>";
        echo "<p>Please run the database update script to add the parent_id column.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
}

// Test 2: Check if relationship column exists in user_profiles table
echo "<h3>Test 2: Checking user_profiles table</h3>";
try {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM user_profiles LIKE 'relationship'");
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result) {
        echo "<p style='color: green;'>PASS: relationship column exists in user_profiles table</p>";
    } else {
        echo "<p style='color: red;'>FAIL: relationship column does not exist in user_profiles table</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
}

// Test 3: Display sample data
echo "<h3>Test 3: Sample data</h3>";
try {
    // Get a sample parent
    $stmt = $pdo->prepare("SELECT id, full_name, email FROM users WHERE role = 'parent' LIMIT 1");
    $stmt->execute();
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($parent) {
        echo "<p>Sample parent: " . htmlspecialchars($parent['full_name']) . " (" . htmlspecialchars($parent['email']) . ")</p>";
    } else {
        echo "<p>No parent found in database</p>";
    }
    
    // Get a sample student with parent_id
    $stmt = $pdo->prepare("SELECT s.id, s.full_name, s.parent_id, u.full_name as parent_name FROM students s LEFT JOIN users u ON s.parent_id = u.id LIMIT 1");
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($student) {
        echo "<p>Sample student: " . htmlspecialchars($student['full_name']) . "</p>";
        if ($student['parent_id']) {
            echo "<p>Linked to parent: " . htmlspecialchars($student['parent_name']) . "</p>";
        } else {
            echo "<p>Not linked to any parent</p>";
        }
    } else {
        echo "<p>No student found in database</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='auth/php/register.php'>Go to Registration Page</a></p>";
?>