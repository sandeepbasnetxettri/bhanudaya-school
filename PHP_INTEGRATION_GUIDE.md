# PHP Integration Guide

This guide explains how to implement the same functionality using PHP instead of the current JavaScript/Supabase implementation.

## Prerequisites

1. PHP 7.4 or higher
2. MySQL or PostgreSQL database
3. Web server (Apache/Nginx)

## Database Setup

### 1. Create Database Tables

```sql
-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student', 'parent') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User Profiles table
CREATE TABLE user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say'),
    reading_habits TEXT,
    exercise_habits TEXT,
    sleep_habits VARCHAR(50),
    occupation VARCHAR(100),
    location VARCHAR(255),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say'),
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(255),
    enrollment_date DATE,
    grade_level VARCHAR(50),
    parent_guardian_name VARCHAR(255),
    emergency_contact VARCHAR(255),
    medical_information TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Teachers table
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    employee_id VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say'),
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(255),
    hire_date DATE,
    qualification VARCHAR(255),
    department VARCHAR(100),
    subjects_taught TEXT,
    salary DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Classes table
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    class_code VARCHAR(50) UNIQUE NOT NULL,
    grade_level VARCHAR(50),
    section VARCHAR(50),
    academic_year VARCHAR(20),
    teacher_id INT,
    room_number VARCHAR(50),
    schedule TEXT,
    max_students INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);

-- Subjects table
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    credits INT,
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Class Subjects junction table
CREATE TABLE class_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT,
    schedule TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(class_id, subject_id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);

-- Enrollments table
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    enrollment_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('active', 'completed', 'dropped') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, class_id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

-- Results table
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    exam_name VARCHAR(100),
    exam_date DATE,
    marks_obtained DECIMAL(5,2),
    total_marks DECIMAL(5,2),
    grade VARCHAR(10),
    percentage DECIMAL(5,2),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

-- Attendance table
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'excused') NOT NULL,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, class_id, date),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

-- Notices table
CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    notice_type ENUM('academic', 'administrative', 'event', 'holiday', 'general') NOT NULL,
    posted_by INT,
    target_audience TEXT,
    start_date DATE,
    end_date DATE,
    is_published BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id)
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_type ENUM('academic', 'cultural', 'sports', 'meeting', 'holiday', 'other') NOT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    location VARCHAR(255),
    organizer VARCHAR(255),
    is_all_day BOOLEAN DEFAULT FALSE,
    is_recurring BOOLEAN DEFAULT FALSE,
    recurrence_pattern VARCHAR(50),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Gallery table
CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_url VARCHAR(500) NOT NULL,
    thumbnail_url VARCHAR(500),
    category ENUM('events', 'activities', 'achievements', 'facilities', 'staff', 'students', 'other') NOT NULL,
    event_id INT,
    uploaded_by INT,
    upload_date DATE DEFAULT (CURRENT_DATE),
    tags TEXT,
    is_published BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Assignments table
CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    subject_id INT,
    class_id INT,
    assigned_by INT,
    assigned_date DATE DEFAULT (CURRENT_DATE),
    due_date DATE NOT NULL,
    max_marks DECIMAL(5,2),
    attachment_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (assigned_by) REFERENCES teachers(id)
);

-- Submitted Assignments table
CREATE TABLE submitted_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    submitted_file_url VARCHAR(500),
    remarks TEXT,
    marks_obtained DECIMAL(5,2),
    graded_by INT,
    graded_date TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(assignment_id, student_id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (graded_by) REFERENCES teachers(id)
);
```

## PHP Backend Implementation

### 1. Database Connection (`config/database.php`)

```php
<?php
$host = 'localhost';
$dbname = 'school_management';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

### 2. User Registration (`api/register.php`)

```php
<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (empty($data['fullName']) || empty($data['email']) || empty($data['password'])) {
        echo json_encode(['error' => 'Full name, email, and password are required']);
        exit;
    }
    
    // Hash password
    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (email, full_name, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['email'], $data['fullName'], $passwordHash, 'student']);
        $userId = $pdo->lastInsertId();
        
        // Insert user profile
        $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, date_of_birth, gender, reading_habits, exercise_habits, sleep_habits, occupation, location, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $data['dob'] ?? null,
            $data['gender'] ?? null,
            json_encode($data['readingHabits'] ?? []),
            json_encode($data['exerciseHabits'] ?? []),
            $data['sleepHabits'] ?? null,
            $data['occupation'] ?? null,
            $data['location'] ?? null,
            $data['address'] ?? null
        ]);
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'message' => 'User registered successfully']);
    } catch(Exception $e) {
        $pdo->rollback();
        echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
```

### 3. User Login (`api/login.php`)

```php
<?php
require_once '../config/database.php';

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['email']) || empty($data['password'])) {
        echo json_encode(['error' => 'Email and password are required']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, email, full_name, password_hash, role FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($data['password'], $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            echo json_encode(['error' => 'Invalid email or password']);
        }
    } catch(Exception $e) {
        echo json_encode(['error' => 'Login failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
```

### 4. Admin Dashboard API Endpoints

Create separate PHP files for each admin functionality:
- `api/manage_notices.php`
- `api/manage_results.php`
- `api/manage_gallery.php`
- `api/manage_teachers.php`
- `api/manage_admissions.php`
- `api/manage_events.php`
- `api/manage_timetable.php`
- `api/manage_users.php`

## Frontend Integration

Update the JavaScript files to call PHP endpoints instead of Supabase:

### Update `js/signup.js`

```javascript
// Replace Supabase call with PHP endpoint
fetch('../api/register.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(userData)
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        showMessage('Account created successfully! Redirecting to login...', 'success');
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 2000);
    } else {
        showMessage('Error: ' + data.error, 'error');
    }
})
.catch(error => {
    showMessage('Error: ' + error.message, 'error');
});
```

### Update `js/login.js`

```javascript
// Replace Supabase call with PHP endpoint
fetch('../api/login.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({email, password})
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        showMessage('Login successful! Redirecting...', 'success');
        setTimeout(() => {
            if (data.user.role === 'admin') {
                window.location.href = 'admin-login.html';
            } else if (data.user.role === 'student') {
                window.location.href = 'student-portal.html';
            } else {
                window.location.href = '../index.html';
            }
        }, 1500);
    } else {
        showMessage('Error: ' + data.error, 'error');
    }
})
.catch(error => {
    showMessage('Error: ' + error.message, 'error');
});
```

## Security Considerations

1. Always validate and sanitize user input
2. Use prepared statements to prevent SQL injection
3. Implement proper session management
4. Use HTTPS in production
5. Implement CSRF protection
6. Set proper HTTP headers for security

## Deployment

1. Upload all files to your web server
2. Create the database and import the SQL schema
3. Update database credentials in `config/database.php`
4. Set proper file permissions
5. Configure your web server (Apache/Nginx)

This PHP implementation provides the same functionality as the current JavaScript/Supabase implementation but uses traditional server-side processing.