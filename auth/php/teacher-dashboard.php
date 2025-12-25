<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

// Database connection
require_once '../../config/dbconnection.php';

$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];
$userId = $_SESSION['user_id'];

// Get teacher ID
try {
    $stmt = $pdo->prepare("SELECT id FROM teachers WHERE user_id = ?");
    $stmt->execute([$userId]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    $teacherId = $teacher['id'] ?? null;
} catch (PDOException $e) {
    $teacherId = null;
}

// Get classes assigned count
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM classes WHERE teacher_id = ?");
    $stmt->execute([$teacherId]);
    $classesAssigned = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
} catch (PDOException $e) {
    $classesAssigned = 4; // Default value
}

// Get pending assignments count
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM assignments WHERE assigned_by = ? AND due_date >= CURDATE()");
    $stmt->execute([$teacherId]);
    $pendingAssignments = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
} catch (PDOException $e) {
    $pendingAssignments = 12; // Default value
}

// Get students count
try {
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT s.id) as count FROM students s JOIN enrollments e ON s.id = e.student_id JOIN classes c ON e.class_id = c.id WHERE c.teacher_id = ?");
    $stmt->execute([$teacherId]);
    $studentsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
} catch (PDOException $e) {
    $studentsCount = 120; // Default value
}

// Get upcoming classes count
try {
    // This is a simplified query - in a real system, you might have a separate schedule table
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM classes WHERE teacher_id = ?");
    $stmt->execute([$teacherId]);
    $upcomingClasses = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
} catch (PDOException $e) {
    $upcomingClasses = 5; // Default value
}

// Get today's schedule
try {
    $stmt = $pdo->prepare("SELECT c.class_name, c.room_number, s.subject_name, c.schedule FROM classes c JOIN subjects s ON c.id = s.id WHERE c.teacher_id = ? LIMIT 3");
    $stmt->execute([$teacherId]);
    $todaysSchedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Default schedule
    $todaysSchedule = [
        ['subject_name' => 'Mathematics', 'class_name' => 'Grade 10A', 'room_number' => 'Room 101', 'schedule' => '09:00 - 10:00'],
        ['subject_name' => 'Science', 'class_name' => 'Grade 9B', 'room_number' => 'Lab 2', 'schedule' => '10:15 - 11:15'],
        ['subject_name' => 'Mathematics', 'class_name' => 'Grade 10B', 'room_number' => 'Room 205', 'schedule' => '11:30 - 12:30']
    ];
}

// Get pending assignments
try {
    $stmt = $pdo->prepare("SELECT a.id, a.title, a.due_date, a.attachment_url, c.class_name, s.subject_name FROM assignments a JOIN classes c ON a.class_id = c.id JOIN subjects s ON a.subject_id = s.id WHERE a.assigned_by = ? AND a.due_date >= CURDATE() ORDER BY a.due_date LIMIT 5");
    $stmt->execute([$teacherId]);
    $pendingAssignmentsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Default assignments
    $pendingAssignmentsList = [
        ['id' => 1, 'title' => 'Algebra Homework - Grade 10A', 'due_date' => '2025-12-20', 'attachment_url' => '', 'class_name' => 'Grade 10A', 'subject_name' => 'Mathematics'],
        ['id' => 2, 'title' => 'Physics Lab Report - Grade 9B', 'due_date' => '2025-12-22', 'attachment_url' => '', 'class_name' => 'Grade 9B', 'subject_name' => 'Science'],
        ['id' => 3, 'title' => 'Geometry Problems - Grade 10B', 'due_date' => '2025-12-25', 'attachment_url' => '', 'class_name' => 'Grade 10B', 'subject_name' => 'Mathematics']
    ];
}

// Handle homework creation
$homeworkMessage = '';
$homeworkError = '';

if (isset($_POST['create_homework'])) {
    $title = trim($_POST['homework_title']);
    $description = trim($_POST['homework_description']);
    $subjectId = (int)$_POST['subject_id'];
    $classId = (int)$_POST['class_id'];
    $dueDate = $_POST['due_date'];
    
    // Handle file upload
    $attachmentUrl = null;
    if (isset($_FILES['homework_attachment']) && $_FILES['homework_attachment']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/assignments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['homework_attachment']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $fileName = uniqid() . '_' . basename($_FILES['homework_attachment']['name']);
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['homework_attachment']['tmp_name'], $uploadPath)) {
                $attachmentUrl = 'uploads/assignments/' . $fileName;
            } else {
                $homeworkError = "Failed to upload attachment file.";
            }
        } else {
            $homeworkError = "Invalid file type. Only PDF, DOC, DOCX, JPG, JPEG, and PNG files are allowed.";
        }
    }
    
    // Validate input
    if (empty($title) || empty($subjectId) || empty($classId) || empty($dueDate)) {
        $homeworkError = "Title, subject, class, and due date are required.";
    } else {
        try {
            // Create new assignment
            if ($attachmentUrl) {
                $stmt = $pdo->prepare("INSERT INTO assignments (title, description, subject_id, class_id, assigned_by, due_date, attachment_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $subjectId, $classId, $teacherId, $dueDate, $attachmentUrl]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO assignments (title, description, subject_id, class_id, assigned_by, due_date) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $subjectId, $classId, $teacherId, $dueDate]);
            }
            
            $homeworkMessage = "Homework created successfully.";
        } catch (PDOException $e) {
            $homeworkError = "Database error: " . $e->getMessage();
        }
    }
}

// Get classes for dropdown
try {
    $stmt = $pdo->prepare("SELECT c.id, c.class_name, s.subject_name FROM classes c JOIN subjects s ON c.id = s.id WHERE c.teacher_id = ?");
    $stmt->execute([$teacherId]);
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $classes = [];
}

// Get subjects for dropdown
try {
    $stmt = $pdo->prepare("SELECT id, subject_name FROM subjects");
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $subjects = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/portal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card i {
            font-size: 2.5rem;
            color: #4CAF50;
            margin-bottom: 15px;
        }
        
        .stat-card h3 {
            margin: 10px 0;
            color: #333;
            font-size: 1.1rem;
        }
        
        .stat-card p {
            font-size: 2rem;
            font-weight: bold;
            color: #4CAF50;
            margin: 0;
        }
        
        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .portal-widget {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .widget-header {
            background: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        
        .widget-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }
        
        .widget-content {
            padding: 20px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        
        .widget-content {
            padding: 20px;
        }
        
        .schedule-list, .assignments-list, .notices-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .schedule-list li, .assignments-list li, .notices-list li {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .schedule-list li:last-child, .assignments-list li:last-child, .notices-list li:last-child {
            border-bottom: none;
        }
        
        .time, .due-date, .notice-date {
            display: block;
            font-size: 0.9rem;
            color: #666;
        }
        
        .subject, .assignment-title, .notice-title {
            display: block;
            font-weight: 600;
            margin: 5px 0;
        }
        
        .room {
            display: block;
            font-size: 0.9rem;
            color: #4CAF50;
        }
        
        .priority {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .priority.high {
            background: #f44336;
            color: white;
        }
        
        .priority.medium {
            background: #ff9800;
            color: white;
        }
        
        .priority.low {
            background: #4CAF50;
            color: white;
        }
        
        /* Homework form styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .form-control::placeholder {
            color: #999;
        }
        
        input[type="date"]::-webkit-inner-spin-button,
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 1;
            cursor: pointer;
        }
        
        input[type="date"] {
            appearance: none;
            -webkit-appearance: none;
        }
        
        .focused .form-control {
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #4CAF50;
            color: #4CAF50;
        }
        
        .btn-outline:hover {
            background: #4CAF50;
            color: white;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .file-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-input-button {
            padding: 12px 20px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .file-input-button:hover {
            background: #e0e0e0;
        }
        
        .file-input-text {
            color: #666;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-col {
            flex: 1;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .portal-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="top-bar">
            <div class="container">
                <div class="top-info">
                    <span><i class="fas fa-phone"></i> +977-1-4567890</span>
                    <span><i class="fas fa-envelope"></i> bhanudayahss071@gmail.com</span>
                </div>
                <div class="top-links">
                    <a href="homework-management.php"><i class="fas fa-book"></i> Homework</a>
                    <a href="pdf-solver.php"><i class="fas fa-file-pdf"></i> PDF Solver</a>
                    <a href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
        
        <div class="main-header">
            <div class="container">
                <div class="header-content">
                    <div class="logo-section">
                        <img src="../../images/school-logo.png" alt="School Logo" class="logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%234CAF50%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2240%22 fill=%22white%22%3ES%3C/text%3E%3C/svg%3E'">
                        <div class="school-info">
                            <h1>Bhanudaya Secondary School</h1>
                            <p class="tagline"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </nav>
    </header>

    <section class="portal-header">
        <div class="container">
            <h1><i class="fas fa-chalkboard-teacher"></i> Teacher Dashboard</h1>
            <p>Welcome back, <span id="teacherName"><?php echo htmlspecialchars($userName); ?></span>!</p>
        </div>
    </section>

    <section class="portal-content">
        <div class="container">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3>Classes Assigned</h3>
                    <p><?php echo $classesAssigned; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-tasks"></i>
                    <h3>Pending Assignments</h3>
                    <p><?php echo $pendingAssignments; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-file-alt"></i>
                    <h3>Students</h3>
                    <p><?php echo $studentsCount; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Upcoming Classes</h3>
                    <p><?php echo $upcomingClasses; ?></p>
                </div>
            </div>

            <?php if ($homeworkMessage): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($homeworkMessage); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($homeworkError): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($homeworkError); ?>
            </div>
            <?php endif; ?>

            <div class="portal-widget">
                <div class="widget-header">
                    <h3><i class="fas fa-book"></i> Create New Homework</h3>
                </div>
                <div class="widget-content">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="homework_title">Homework Title *</label>
                            <input type="text" id="homework_title" name="homework_title" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="homework_description">Description</label>
                            <textarea id="homework_description" name="homework_description" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="subject_id">Subject *</label>
                                    <select id="subject_id" name="subject_id" class="form-control" required>
                                        <option value="">Select Subject</option>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['subject_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="class_id">Class *</label>
                                    <select id="class_id" name="class_id" class="form-control" required>
                                        <option value="">Select Class</option>
                                        <?php foreach ($classes as $class): ?>
                                            <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?> (<?php echo htmlspecialchars($class['subject_name']); ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="due_date">Due Date *</label>
                                    <input type="date" id="due_date" name="due_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="homework_attachment">Attachment (PDF, DOC, DOCX, JPG, PNG)</label>
                            <div class="file-input-wrapper">
                                <input type="file" id="homework_attachment" name="homework_attachment" class="file-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <div class="file-input-button">
                                    <i class="fas fa-folder-open"></i>
                                    <span>Choose File</span>
                                </div>
                                <div class="file-input-text" id="file-input-text">No file chosen</div>
                            </div>
                        </div>
                        
                        <button type="submit" name="create_homework" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                            <i class="fas fa-paper-plane"></i> Assign Homework
                        </button>
                    </form>
                </div>
            </div>

            <div class="portal-grid">
                <div class="portal-widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-calendar-alt"></i> Today's Schedule</h3>
                    </div>
                    <div class="widget-content">
                        <ul class="schedule-list">
                            <?php if (!empty($todaysSchedule)): ?>
                                <?php foreach ($todaysSchedule as $schedule): ?>
                                <li>
                                    <span class="time"><?php echo htmlspecialchars($schedule['schedule']); ?></span>
                                    <span class="subject"><?php echo htmlspecialchars($schedule['subject_name']); ?> - <?php echo htmlspecialchars($schedule['class_name']); ?></span>
                                    <span class="room"><?php echo htmlspecialchars($schedule['room_number']); ?></span>
                                </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>
                                    <span class="time">09:00 - 10:00</span>
                                    <span class="subject">Mathematics - Grade 10A</span>
                                    <span class="room">Room 101</span>
                                </li>
                                <li>
                                    <span class="time">10:15 - 11:15</span>
                                    <span class="subject">Science - Grade 9B</span>
                                    <span class="room">Lab 2</span>
                                </li>
                                <li>
                                    <span class="time">11:30 - 12:30</span>
                                    <span class="subject">Mathematics - Grade 10B</span>
                                    <span class="room">Room 205</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="portal-widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-tasks"></i> Pending Assignments</h3>
                    </div>
                    <div class="widget-content">
                        <ul class="assignments-list">
                            <?php if (!empty($pendingAssignmentsList)): ?>
                                <?php foreach ($pendingAssignmentsList as $assignment): ?>
                                <li>
                                    <span class="assignment-title"><?php echo htmlspecialchars($assignment['title']); ?></span>
                                    <span class="subject"><?php echo htmlspecialchars($assignment['subject_name']); ?> - <?php echo htmlspecialchars($assignment['class_name']); ?></span>
                                    <span class="due-date">Due: <?php echo date('M j, Y', strtotime($assignment['due_date'])); ?></span>
                                    <?php if (!empty($assignment['attachment_url'])): ?>
                                        <a href="../../<?php echo htmlspecialchars($assignment['attachment_url']); ?>" target="_blank" class="btn btn-outline" style="margin-top: 10px; display: inline-block;">
                                            <i class="fas fa-download"></i> Download Attachment
                                        </a>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>
                                    <span class="assignment-title">Algebra Homework - Grade 10A</span>
                                    <span class="subject">Mathematics - Grade 10A</span>
                                    <span class="due-date">Due: Dec 20, 2025</span>
                                </li>
                                <li>
                                    <span class="assignment-title">Physics Lab Report - Grade 9B</span>
                                    <span class="subject">Science - Grade 9B</span>
                                    <span class="due-date">Due: Dec 22, 2025</span>
                                </li>
                                <li>
                                    <span class="assignment-title">Geometry Problems - Grade 10B</span>
                                    <span class="subject">Mathematics - Grade 10B</span>
                                    <span class="due-date">Due: Dec 25, 2025</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="portal-widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-bell"></i> Recent Notices</h3>
                    </div>
                    <div class="widget-content">
                        <ul class="notices-list">
                            <li>
                                <span class="notice-title">Faculty Meeting - Dec 22</span>
                                <span class="notice-date">Posted 2 days ago</span>
                            </li>
                            <li>
                                <span class="notice-title">New Grading Policy</span>
                                <span class="notice-date">Posted 1 week ago</span>
                            </li>
                            <li>
                                <span class="notice-title">Professional Development Workshop</span>
                                <span class="notice-date">Posted 2 weeks ago</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="portal-widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-chart-bar"></i> Class Performance</h3>
                    </div>
                    <div class="widget-content">
                        <div class="performance-chart">
                            <canvas id="performanceChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>About Us</h3>
                    <p>Excellence School is committed to providing quality education and nurturing young minds for a successful future.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>Contact Info</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> Binayi Triveni Rural Municipality, Dumkibas, Nawalaparasi, Nepal</li>
                        <li><i class="fas fa-phone"></i> +977-1-4567890</li>
                        <li><i class="fas fa-envelope"></i> bhanudayahss071@gmail.com</li>
                        <li><i class="fas fa-clock"></i> Sun-Fri: 9:00 AM - 4:00 PM</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Excellence School. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../js/student-portal.js"></script>
    <script>
        // Add any additional JavaScript functionality here
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Add animation to widgets when they come into view
            const widgets = document.querySelectorAll('.portal-widget');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            widgets.forEach(widget => {
                widget.style.opacity = 0;
                widget.style.transform = 'translateY(20px)';
                widget.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(widget);
            });
            
            // Set min date for due date input to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('due_date').setAttribute('min', today);
            
            // Enhanced file input handling
            const fileInput = document.getElementById('homework_attachment');
            const fileInputText = document.getElementById('file-input-text');
            
            if (fileInput && fileInputText) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        const fileName = this.files[0].name;
                        const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2); // Size in MB
                        fileInputText.textContent = `${fileName} (${fileSize} MB)`;
                        fileInputText.title = fileName;
                    } else {
                        fileInputText.textContent = 'No file chosen';
                    }
                });
            }
            
            // Form validation
            const homeworkForm = document.querySelector('form');
            if (homeworkForm) {
                homeworkForm.addEventListener('submit', function(e) {
                    const title = document.getElementById('homework_title');
                    const subject = document.getElementById('subject_id');
                    const classSelect = document.getElementById('class_id');
                    const dueDate = document.getElementById('due_date');
                    
                    if (!title.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a homework title');
                        title.focus();
                        return false;
                    }
                    
                    if (!subject.value) {
                        e.preventDefault();
                        alert('Please select a subject');
                        subject.focus();
                        return false;
                    }
                    
                    if (!classSelect.value) {
                        e.preventDefault();
                        alert('Please select a class');
                        classSelect.focus();
                        return false;
                    }
                    
                    if (!dueDate.value) {
                        e.preventDefault();
                        alert('Please select a due date');
                        dueDate.focus();
                        return false;
                    }
                    
                    // Check if due date is in the past
                    const selectedDate = new Date(dueDate.value);
                    const currentDate = new Date();
                    currentDate.setHours(0, 0, 0, 0);
                    
                    if (selectedDate < currentDate) {
                        e.preventDefault();
                        alert('Due date cannot be in the past');
                        dueDate.focus();
                        return false;
                    }
                });
            }
            
            // Add focus effects to form controls
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                control.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
        });
    </script>
</body>
</html>
