-- Script to update foreign key constraints to use CASCADE DELETE
-- This will automatically delete related records when a student is deleted

-- First, we need to drop existing foreign key constraints
-- Note: This needs to be done in the correct order to avoid dependency issues

-- Drop foreign key constraints from submitted_assignments
ALTER TABLE submitted_assignments DROP FOREIGN KEY submitted_assignments_ibfk_1;

-- Drop foreign key constraints from attendance
ALTER TABLE attendance DROP FOREIGN KEY attendance_ibfk_1;

-- Drop foreign key constraints from results
ALTER TABLE results DROP FOREIGN KEY results_ibfk_1;

-- Drop foreign key constraints from enrollments
ALTER TABLE enrollments DROP FOREIGN KEY enrollments_ibfk_1;

-- Now recreate the foreign key constraints with CASCADE DELETE
-- Results table
ALTER TABLE results 
ADD CONSTRAINT fk_results_student 
FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE;

-- Attendance table
ALTER TABLE attendance 
ADD CONSTRAINT fk_attendance_student 
FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE;

-- Enrollments table
ALTER TABLE enrollments 
ADD CONSTRAINT fk_enrollments_student 
FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE;

-- Submitted assignments table
ALTER TABLE submitted_assignments 
ADD CONSTRAINT fk_submitted_assignments_student 
FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE;

-- Also update the user_id foreign key in students table to cascade delete
ALTER TABLE students 
DROP FOREIGN KEY students_ibfk_1;

ALTER TABLE students 
ADD CONSTRAINT fk_students_user 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;