<?php
// Test script to verify the registration fix

// Include the database connection
require_once 'config/dbconnection.php';

// Test data
$userId = 123;
$fullName = "Test Teacher";
$phone = "1234567890";
$email = "test@example.com";

// Generate employee ID
$employeeId = 'EMP' . str_pad($userId, 6, '0', STR_PAD_LEFT);
echo "Generated Employee ID: " . $employeeId . "\n";

// Generate student ID
$studentId = 'STU' . str_pad($userId, 6, '0', STR_PAD_LEFT);
echo "Generated Student ID: " . $studentId . "\n";

// Test inserting into teachers table
try {
    $stmt = $pdo->prepare("INSERT INTO teachers (user_id, employee_id, full_name, phone, email) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$userId, $employeeId, $fullName, $phone, $email]);
    echo "Successfully inserted teacher record\n";
    
    // Clean up test record
    $stmt = $pdo->prepare("DELETE FROM teachers WHERE employee_id = ?");
    $stmt->execute([$employeeId]);
} catch (PDOException $e) {
    echo "Error inserting teacher record: " . $e->getMessage() . "\n";
}

// Test inserting into students table
try {
    $stmt = $pdo->prepare("INSERT INTO students (user_id, student_id, full_name, phone, email) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$userId, $studentId, $fullName, $phone, $email]);
    echo "Successfully inserted student record\n";
    
    // Clean up test record
    $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->execute([$studentId]);
} catch (PDOException $e) {
    echo "Error inserting student record: " . $e->getMessage() . "\n";
}
?>