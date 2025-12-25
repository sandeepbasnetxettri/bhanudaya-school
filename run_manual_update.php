<?php
// Script to manually run the database updates
include_once 'config/dbconnection.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Manual Database Update</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #155724; background-color: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 20px 0; }
        .error { color: #721c24; background-color: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px 0; }
        .info { color: #0c5460; background-color: #d1ecf1; padding: 15px; border: 1px solid #bee5eb; border-radius: 4px; margin: 20px 0; }
        h1 { color: #333; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class='container'>
<h1>Manual Database Update for Parent Portal</h1>";

try {
    // Check and add missing columns to user_profiles table
    
    // Check if phone column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM user_profiles LIKE 'phone'");
    $stmt->execute();
    $column = $stmt->fetch();
    if (!$column) {
        $sql = "ALTER TABLE user_profiles ADD COLUMN phone VARCHAR(20)";
        $pdo->exec($sql);
        echo "<div class='success'>✓ Added phone column to user_profiles table</div>";
    } else {
        echo "<div class='info'>ℹ️ Phone column already exists in user_profiles table</div>";
    }
    
    // Check if date_of_birth column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM user_profiles LIKE 'date_of_birth'");
    $stmt->execute();
    $column = $stmt->fetch();
    if (!$column) {
        $sql = "ALTER TABLE user_profiles ADD COLUMN date_of_birth DATE";
        $pdo->exec($sql);
        echo "<div class='success'>✓ Added date_of_birth column to user_profiles table</div>";
    } else {
        echo "<div class='info'>ℹ️ Date_of_birth column already exists in user_profiles table</div>";
    }
    
    // Check if gender column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM user_profiles LIKE 'gender'");
    $stmt->execute();
    $column = $stmt->fetch();
    if (!$column) {
        $sql = "ALTER TABLE user_profiles ADD COLUMN gender ENUM('male', 'female', 'other', 'prefer_not_to_say')";
        $pdo->exec($sql);
        echo "<div class='success'>✓ Added gender column to user_profiles table</div>";
    } else {
        echo "<div class='info'>ℹ️ Gender column already exists in user_profiles table</div>";
    }
    
    // Check if address column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM user_profiles LIKE 'address'");
    $stmt->execute();
    $column = $stmt->fetch();
    if (!$column) {
        $sql = "ALTER TABLE user_profiles ADD COLUMN address TEXT";
        $pdo->exec($sql);
        echo "<div class='success'>✓ Added address column to user_profiles table</div>";
    } else {
        echo "<div class='info'>ℹ️ Address column already exists in user_profiles table</div>";
    }
    
    // Check if occupation column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM user_profiles LIKE 'occupation'");
    $stmt->execute();
    $column = $stmt->fetch();
    if (!$column) {
        $sql = "ALTER TABLE user_profiles ADD COLUMN occupation VARCHAR(100)";
        $pdo->exec($sql);
        echo "<div class='success'>✓ Added occupation column to user_profiles table</div>";
    } else {
        echo "<div class='info'>ℹ️ Occupation column already exists in user_profiles table</div>";
    }
    
    // Check if relationship column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM user_profiles LIKE 'relationship'");
    $stmt->execute();
    $column = $stmt->fetch();
    if (!$column) {
        $sql = "ALTER TABLE user_profiles ADD COLUMN relationship ENUM('father', 'mother', 'guardian')";
        $pdo->exec($sql);
        echo "<div class='success'>✓ Added relationship column to user_profiles table</div>";
    } else {
        echo "<div class='info'>ℹ️ Relationship column already exists in user_profiles table</div>";
    }
    
    // Check if parent_id column exists in students
    $stmt = $pdo->prepare("SHOW COLUMNS FROM students LIKE 'parent_id'");
    $stmt->execute();
    $parentColumn = $stmt->fetch();
    
    if (!$parentColumn) {
        // Add parent_id column to students table
        $sql = "ALTER TABLE students ADD COLUMN parent_id INT, ADD INDEX(parent_id), ADD FOREIGN KEY (parent_id) REFERENCES users(id)";
        $pdo->exec($sql);
        echo "<div class='success'>✓ Added parent_id column to students table</div>";
    } else {
        echo "<div class='info'>ℹ️ Parent_id column already exists in students table</div>";
    }
    
    echo "<div class='success'><strong>✅ All database updates completed successfully!</strong></div>";
    echo "<p>You can now access the parent portal pages without errors.</p>";
    echo "<p><a href='auth/php/parent-login.php'>Go to Parent Login</a></p>";
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ Error updating database schema: " . $e->getMessage() . "</div>";
    echo "<p>Please check your database configuration and permissions.</p>";
    echo "<p>If you continue to have issues, you can manually run these SQL commands in your database:</p>";
    echo "<pre>";
    echo "ALTER TABLE user_profiles ADD COLUMN phone VARCHAR(20);\n";
    echo "ALTER TABLE user_profiles ADD COLUMN date_of_birth DATE;\n";
    echo "ALTER TABLE user_profiles ADD COLUMN gender ENUM('male', 'female', 'other', 'prefer_not_to_say');\n";
    echo "ALTER TABLE user_profiles ADD COLUMN address TEXT;\n";
    echo "ALTER TABLE user_profiles ADD COLUMN occupation VARCHAR(100);\n";
    echo "ALTER TABLE user_profiles ADD COLUMN relationship ENUM('father', 'mother', 'guardian');\n";
    echo "ALTER TABLE students ADD COLUMN parent_id INT, ADD INDEX(parent_id), ADD FOREIGN KEY (parent_id) REFERENCES users(id);\n";
    echo "</pre>";
}

echo "</div></body></html>";
?>