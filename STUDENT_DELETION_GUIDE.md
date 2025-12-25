# Student Deletion Guide

## Overview
This guide explains how student deletion works in the Bhanudaya Model School management system.

## How Student Deletion Works

### Automatic Cascade Deletion
The system uses database-level CASCADE DELETE constraints to automatically remove all related records when a student is deleted:

- Student results
- Attendance records
- Enrollments
- Submitted assignments

### Manual Deletion Process
If CASCADE DELETE is not enabled, the system manually deletes all related records in the correct order:

1. Submitted assignments
2. Attendance records
3. Results
4. Enrollments
5. Student record

## Implementation Details

### Database Schema
Foreign key constraints have been updated to include `ON DELETE CASCADE`:

```sql
ALTER TABLE results 
ADD CONSTRAINT fk_results_student 
FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE;
```

### PHP Implementation
The student deletion logic in `student-management.php`:

```php
// Handle student deletion
if (isset($_POST['delete_student'])) {
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
        
        // With CASCADE DELETE enabled, deleting the student will automatically
        // delete all related records (results, attendance, enrollments, etc.)
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$studentId]);
        
        // Commit transaction
        $pdo->commit();
        
        $message = "Student '" . $student['full_name'] . "' and all related records deleted successfully.";
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

## User Interface

### Delete Confirmation
Before deletion, users see a confirmation dialog with:
- Student name
- Warning about permanent deletion
- List of affected records
- Cancel option

### Success/Error Messages
After deletion, users receive:
- Success message with student name
- Error message if deletion fails

## Testing

### Test Scripts
Two test scripts are available:
1. `test_student_deletion.php` - Manual testing interface
2. `apply_cascade_deletes.php` - Apply CASCADE DELETE constraints

### How to Test
1. Navigate to `test_student_deletion.php`
2. Select a student to delete
3. Confirm the deletion
4. Verify all related records are removed

## Troubleshooting

### Common Issues
1. **Foreign key constraint errors** - Run `apply_cascade_deletes.php`
2. **Permission denied** - Check database user privileges
3. **Transaction failures** - Check database connectivity

### Solutions
1. Ensure CASCADE DELETE constraints are applied
2. Verify database user has DELETE privileges
3. Check database connection settings in `config/dbconnection.php`