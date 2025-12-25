<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: auth/php/login.php');
    exit;
}

// Database connection
require_once 'config/dbconnection.php';

// Test student deletion
if (isset($_GET['test_delete'])) {
    $studentId = (int)$_GET['student_id'];
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // First check if student exists
        $checkStmt = $pdo->prepare("SELECT full_name FROM students WHERE id = ?");
        $checkStmt->execute([$studentId]);
        $student = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) {
            throw new Exception("Student not found.");
        }
        
        echo "<h2>Deleting student: " . htmlspecialchars($student['full_name']) . "</h2>";
        
        // Count related records before deletion
        $tables = [
            'submitted_assignments' => 'student_id',
            'attendance' => 'student_id',
            'results' => 'student_id',
            'enrollments' => 'student_id'
        ];
        
        $counts = [];
        foreach ($tables as $table => $column) {
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
            $countStmt->execute([$studentId]);
            $counts[$table] = $countStmt->fetchColumn();
            echo "<p>$table: " . $counts[$table] . " records</p>";
        }
        
        // Delete related records
        foreach ($tables as $table => $column) {
            $deleteStmt = $pdo->prepare("DELETE FROM $table WHERE $column = ?");
            $deleteStmt->execute([$studentId]);
            echo "<p>Deleted " . $deleteStmt->rowCount() . " records from $table</p>";
        }
        
        // Delete student
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$studentId]);
        echo "<p>Deleted student record: " . $stmt->rowCount() . " row(s) affected</p>";
        
        // Commit transaction
        $pdo->commit();
        
        echo "<p style='color: green;'>All records deleted successfully!</p>";
        
    } catch (Exception $e) {
        // Rollback transaction
        $pdo->rollBack();
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    } catch (PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
    
    echo "<p><a href='test_student_deletion.php'>Back to test</a></p>";
    exit;
}

// Display list of students for testing
try {
    $stmt = $pdo->query("SELECT id, student_id, full_name, grade_level FROM students ORDER BY full_name");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $students = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Student Deletion</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .warning { color: orange; }
        .danger { color: red; }
    </style>
</head>
<body>
    <h1>Test Student Deletion</h1>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <p class="warning"><strong>Warning:</strong> This is a test page for student deletion. Use with caution!</p>
    
    <?php if (empty($students)): ?>
        <p>No students found in the database.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Grade Level</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['grade_level']); ?></td>
                    <td>
                        <a href="?test_delete=1&student_id=<?php echo $student['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this student and ALL related records? This cannot be undone!')"
                           class="danger">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>