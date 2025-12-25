# Database Schema for Excellence School Management System

This document outlines the database schema for the school management system using Supabase.

## Tables

### 1. Users
Stores all user accounts in the system.

```sql
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email TEXT UNIQUE NOT NULL,
    full_name TEXT NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT CHECK (role IN ('admin', 'teacher', 'student', 'parent')) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 2. User Profiles
Stores additional profile information collected during signup.

```sql
CREATE TABLE user_profiles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    date_of_birth DATE,
    gender TEXT CHECK (gender IN ('male', 'female', 'other', 'prefer_not_to_say')),
    reading_habits TEXT[],
    exercise_habits TEXT[],
    sleep_habits TEXT,
    occupation TEXT,
    location TEXT,
    address TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 3. Students
Stores detailed information about students.

```sql
CREATE TABLE students (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    user_id UUID REFERENCES users(id),
    student_id TEXT UNIQUE NOT NULL,
    full_name TEXT NOT NULL,
    date_of_birth DATE,
    gender TEXT CHECK (gender IN ('male', 'female', 'other', 'prefer_not_to_say')),
    address TEXT,
    phone TEXT,
    email TEXT,
    enrollment_date DATE,
    grade_level TEXT,
    parent_guardian_name TEXT,
    emergency_contact TEXT,
    medical_information TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 4. Teachers
Stores detailed information about teachers.

```sql
CREATE TABLE teachers (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    user_id UUID REFERENCES users(id),
    employee_id TEXT UNIQUE NOT NULL,
    full_name TEXT NOT NULL,
    date_of_birth DATE,
    gender TEXT CHECK (gender IN ('male', 'female', 'other', 'prefer_not_to_say')),
    address TEXT,
    phone TEXT,
    email TEXT,
    hire_date DATE,
    qualification TEXT,
    department TEXT,
    subjects_taught TEXT[],
    salary DECIMAL(10,2),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 5. Classes
Stores information about classes/sections.

```sql
CREATE TABLE classes (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    class_name TEXT NOT NULL,
    class_code TEXT UNIQUE NOT NULL,
    grade_level TEXT,
    section TEXT,
    academic_year TEXT,
    teacher_id UUID REFERENCES teachers(id),
    room_number TEXT,
    schedule TEXT,
    max_students INTEGER,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 6. Subjects
Stores information about subjects/courses.

```sql
CREATE TABLE subjects (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    subject_name TEXT NOT NULL,
    subject_code TEXT UNIQUE NOT NULL,
    description TEXT,
    credits INTEGER,
    department TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 7. Class_Subjects
Junction table for many-to-many relationship between classes and subjects.

```sql
CREATE TABLE class_subjects (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    class_id UUID REFERENCES classes(id),
    subject_id UUID REFERENCES subjects(id),
    teacher_id UUID REFERENCES teachers(id),
    schedule TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(class_id, subject_id)
);
```

### 8. Enrollments
Tracks student enrollments in classes.

```sql
CREATE TABLE enrollments (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    student_id UUID REFERENCES students(id),
    class_id UUID REFERENCES classes(id),
    enrollment_date DATE DEFAULT NOW(),
    status TEXT CHECK (status IN ('active', 'completed', 'dropped')) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(student_id, class_id)
);
```

### 9. Results
Stores student examination results.

```sql
CREATE TABLE results (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    student_id UUID REFERENCES students(id),
    subject_id UUID REFERENCES subjects(id),
    exam_name TEXT,
    exam_date DATE,
    marks_obtained DECIMAL(5,2),
    total_marks DECIMAL(5,2),
    grade TEXT,
    percentage DECIMAL(5,2),
    remarks TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 10. Attendance
Stores student attendance records.

```sql
CREATE TABLE attendance (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    student_id UUID REFERENCES students(id),
    class_id UUID REFERENCES classes(id),
    date DATE NOT NULL,
    status TEXT CHECK (status IN ('present', 'absent', 'late', 'excused')) NOT NULL,
    remarks TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(student_id, class_id, date)
);
```

### 11. Notices
Stores school notices and announcements.

```sql
CREATE TABLE notices (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    notice_type TEXT CHECK (notice_type IN ('academic', 'administrative', 'event', 'holiday', 'general')) NOT NULL,
    posted_by UUID REFERENCES users(id),
    target_audience TEXT[], -- e.g., ['students', 'teachers', 'parents', 'all']
    start_date DATE,
    end_date DATE,
    is_published BOOLEAN DEFAULT false,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 12. Events
Stores school events and activities.

```sql
CREATE TABLE events (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    title TEXT NOT NULL,
    description TEXT,
    event_type TEXT CHECK (event_type IN ('academic', 'cultural', 'sports', 'meeting', 'holiday', 'other')) NOT NULL,
    start_datetime TIMESTAMP WITH TIME ZONE NOT NULL,
    end_datetime TIMESTAMP WITH TIME ZONE NOT NULL,
    location TEXT,
    organizer TEXT,
    is_all_day BOOLEAN DEFAULT false,
    is_recurring BOOLEAN DEFAULT false,
    recurrence_pattern TEXT, -- e.g., 'daily', 'weekly', 'monthly'
    created_by UUID REFERENCES users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 13. Gallery
Stores photos and media for the school gallery.

```sql
CREATE TABLE gallery (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    title TEXT NOT NULL,
    description TEXT,
    file_url TEXT NOT NULL,
    thumbnail_url TEXT,
    category TEXT CHECK (category IN ('events', 'activities', 'achievements', 'facilities', 'staff', 'students', 'other')) NOT NULL,
    event_id UUID REFERENCES events(id),
    uploaded_by UUID REFERENCES users(id),
    upload_date DATE DEFAULT NOW(),
    tags TEXT[],
    is_published BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 14. Assignments
Stores student assignments.

```sql
CREATE TABLE assignments (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    title TEXT NOT NULL,
    description TEXT,
    subject_id UUID REFERENCES subjects(id),
    class_id UUID REFERENCES classes(id),
    assigned_by UUID REFERENCES teachers(id),
    assigned_date DATE DEFAULT NOW(),
    due_date DATE NOT NULL,
    max_marks DECIMAL(5,2),
    attachment_url TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);
```

### 15. Submitted_Assignments
Stores submitted assignments by students.

```sql
CREATE TABLE submitted_assignments (
    id UUID PRIMARY KEY DEFAULT gen_random_uid(),
    assignment_id UUID REFERENCES assignments(id),
    student_id UUID REFERENCES students(id),
    submission_date TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    submitted_file_url TEXT,
    remarks TEXT,
    marks_obtained DECIMAL(5,2),
    graded_by UUID REFERENCES teachers(id),
    graded_date TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(assignment_id, student_id)
);
```

## Relationships

1. **Users** is the central table connecting to Students, Teachers, and other entities
2. **Students** and **Teachers** have one-to-one relationships with **Users**
3. **Classes** are taught by **Teachers**
4. **Subjects** are taught in **Classes** through the **Class_Subjects** junction table
5. **Students** are enrolled in **Classes** through the **Enrollments** table
6. **Results** belong to **Students** and **Subjects**
7. **Attendance** records are linked to **Students** and **Classes**
8. **Notices** and **Events** are created by **Users**
9. **Gallery** items can be linked to **Events**
10. **Assignments** are given by **Teachers** for **Classes** and **Subjects**
11. **Submitted_Assignments** connect **Students** to **Assignments**

## Indexes

```sql
-- Performance indexes
CREATE INDEX idx_students_user_id ON students(user_id);
CREATE INDEX idx_teachers_user_id ON teachers(user_id);
CREATE INDEX idx_classes_teacher_id ON classes(teacher_id);
CREATE INDEX idx_enrollments_student_id ON enrollments(student_id);
CREATE INDEX idx_enrollments_class_id ON enrollments(class_id);
CREATE INDEX idx_results_student_id ON results(student_id);
CREATE INDEX idx_results_subject_id ON results(subject_id);
CREATE INDEX idx_attendance_student_id ON attendance(student_id);
CREATE INDEX idx_attendance_class_id ON attendance(class_id);
CREATE INDEX idx_attendance_date ON attendance(date);
CREATE INDEX idx_notices_created_at ON notices(created_at);
CREATE INDEX idx_events_start_datetime ON events(start_datetime);
```

This schema provides a comprehensive foundation for a school management system with proper relationships and data integrity.