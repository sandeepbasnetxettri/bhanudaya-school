<?php
// Script to apply CASCADE DELETE constraints to the database
require_once 'config/dbconnection.php';

echo "<h1>Applying CASCADE DELETE Constraints</h1>";

try {
    // Start transaction
    $pdo->beginTransaction();
    
    echo "<p>Starting to update foreign key constraints...</p>";
    
    // Drop existing foreign key constraints
    $dropConstraints = [
        "ALTER TABLE submitted_assignments DROP FOREIGN KEY submitted_assignments_ibfk_1",
        "ALTER TABLE attendance DROP FOREIGN KEY attendance_ibfk_1",
        "ALTER TABLE results DROP FOREIGN KEY results_ibfk_1",
        "ALTER TABLE enrollments DROP FOREIGN KEY enrollments_ibfk_1",
        "ALTER TABLE students DROP FOREIGN KEY students_ibfk_1"
    ];
    
    foreach ($dropConstraints as $sql) {
        try {
            $pdo->exec($sql);
            echo "<p>Dropped constraint: " . htmlspecialchars($sql) . "</p>";
        } catch (PDOException $e) {
            echo "<p style='color: orange;'>Warning: " . $e->getMessage() . "</p>";
        }
    }
    
    // Add new foreign key constraints with CASCADE DELETE
    $addConstraints = [
        "ALTER TABLE results ADD CONSTRAINT fk_results_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE",
        "ALTER TABLE attendance ADD CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE",
        "ALTER TABLE enrollments ADD CONSTRAINT fk_enrollments_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE",
        "ALTER TABLE submitted_assignments ADD CONSTRAINT fk_submitted_assignments_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE",
        "ALTER TABLE students ADD CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE"
    ];
    
    foreach ($addConstraints as $sql) {
        try {
            $pdo->exec($sql);
            echo "<p>Added constraint: " . htmlspecialchars($sql) . "</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Error adding constraint: " . $e->getMessage() . "</p>";
            throw $e;
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo "<p style='color: green;'><strong>All constraints updated successfully!</strong></p>";
    echo "<p>Now when a student is deleted, all related records will be automatically deleted as well.</p>";
    
} catch (PDOException $e) {
    // Rollback transaction
    $pdo->rollBack();
    echo "<p style='color: red;'><strong>Error updating constraints:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please check the database schema and try again.</p>";
}

echo "<p><a href='index.php'>Back to Home</a></p>";
?>