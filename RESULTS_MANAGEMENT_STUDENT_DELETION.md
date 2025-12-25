# Results Management - Student Deletion Guide

## Overview
This guide explains how to delete students and their associated results in the Results Management system.

## Features

### Individual Result Deletion
- Delete specific exam results for students
- Maintains student records while removing only the result data
- Provides confirmation before deletion

### Student Deletion with Results
- Delete a student and all their associated results
- Completely removes student from the system
- Shows warning about irreversible action

## How to Delete a Student

### From Results Management Page
1. Navigate to Results Management page
2. Click the "Delete Student" button in the top right corner
3. Select a student from the dropdown list
4. Review the warning message about irreversible actions
5. Confirm deletion by clicking "Yes, Delete Student"

### What Happens During Student Deletion
1. System counts all results associated with the student
2. Deletes all results for that student from the database
3. Removes the student record from the database
4. Displays success message with deletion details

## Implementation Details

### Database Operations
```sql
-- Count results for student
SELECT COUNT(*) FROM results WHERE student_id = ?

-- Delete all results for student
DELETE FROM results WHERE student_id = ?

-- Delete student record
DELETE FROM students WHERE id = ?
```

### PHP Implementation
```php
// Handle student deletion with all related results
if (isset($_POST['delete_student_with_results'])) {
    $studentId = (int)$_POST['student_id'];
    
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
        
        // Count results for this student
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM results WHERE student_id = ?");
        $countStmt->execute([$studentId]);
        $resultsCount = $countStmt->fetchColumn();
        
        // Delete all results for this student
        $deleteResultsStmt = $pdo->prepare("DELETE FROM results WHERE student_id = ?");
        $deleteResultsStmt->execute([$studentId]);
        
        // Delete student
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$studentId]);
        
        // Commit transaction
        $pdo->commit();
        
        $message = "Student '" . $student['full_name'] . "' and " . $resultsCount . " associated result(s) deleted successfully.";
    } catch (Exception $e) {
        // Rollback transaction
        $pdo->rollBack();
        $error = "Error deleting student: " . $e->getMessage();
    } catch (PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        $error = "Database error: " . $e->getMessage();
    }
}
```

## User Interface Elements

### Delete Student Button
Located in the top right corner of the Results Management page:
```html
<button id="showStudentDeleteModal" class="btn btn-primary" style="background-color: #dc3545; margin-left: 10px;">
    <i class="fas fa-user-times"></i> Delete Student
</button>
```

### Delete Student Modal
Features:
- Student selection dropdown with class information
- Detailed warning about consequences
- Clear confirmation process

## Security Measures

### Transaction Safety
- Uses database transactions to ensure data consistency
- Rolls back changes if any error occurs
- Prevents partial deletions

### Data Validation
- Verifies student exists before deletion
- Checks for proper permissions (admin only)
- Validates input data

### Error Handling
- Catches exceptions and database errors
- Provides user-friendly error messages
- Maintains system stability

## Best Practices

### Before Deleting a Student
1. Ensure you have the correct student selected
2. Backup database if needed
3. Verify no other systems depend on this data

### After Deleting a Student
1. Check that all related data was removed
2. Verify system functionality
3. Monitor for any unexpected issues

## Troubleshooting

### Common Issues
1. **Student not found error** - Verify student exists in database
2. **Database permission error** - Check user privileges
3. **Transaction failure** - Check database connectivity

### Solutions
1. Refresh the page to reload student list
2. Contact system administrator for permissions
3. Check database connection settings