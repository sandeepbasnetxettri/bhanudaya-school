<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Database connection
require_once '../../config/dbconnection.php';

// Handle form submissions
$message = '';
$error = '';

// Handle result creation
if (isset($_POST['create_result'])) {
    $studentId = trim($_POST['student_id']);
    $subjectId = trim($_POST['subject_id']);
    $examName = trim($_POST['exam_name']);
    $examDate = $_POST['exam_date'];
    $marksObtained = (float)$_POST['marks_obtained'];
    $totalMarks = (float)$_POST['total_marks'];
    $grade = trim($_POST['grade']);
    $remarks = trim($_POST['remarks']);
    
    // No file upload needed as certificate_path column doesn't exist
    
    // Validate input
    if (empty($studentId) || empty($subjectId) || empty($examName) || empty($examDate) || $marksObtained === '' || $totalMarks === '') {
        $error = "All fields except grade and remarks are required.";
    } elseif ($marksObtained > $totalMarks) {
        $error = "Marks obtained cannot be greater than total marks.";
    } else {
        try {
            // Calculate percentage
            $percentage = ($marksObtained / $totalMarks) * 100;
            
            // Create new result
            $stmt = $pdo->prepare("INSERT INTO results (student_id, subject_id, exam_name, exam_date, marks_obtained, total_marks, grade, percentage, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$studentId, $subjectId, $examName, $examDate, $marksObtained, $totalMarks, $grade, $percentage, $remarks]);
            
            $message = "Result created successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle result updates
if (isset($_POST['update_result'])) {
    $resultId = (int)$_POST['result_id'];
    $studentId = trim($_POST['student_id']);
    $subjectId = trim($_POST['subject_id']);
    $examName = trim($_POST['exam_name']);
    $examDate = $_POST['exam_date'];
    $marksObtained = (float)$_POST['marks_obtained'];
    $totalMarks = (float)$_POST['total_marks'];
    $grade = trim($_POST['grade']);
    $remarks = trim($_POST['remarks']);
    
    // No file upload needed as certificate_path column doesn't exist
    
    // Validate input
    if (empty($studentId) || empty($subjectId) || empty($examName) || empty($examDate) || $marksObtained === '' || $totalMarks === '') {
        $error = "All fields except grade and remarks are required.";
    } elseif ($marksObtained > $totalMarks) {
        $error = "Marks obtained cannot be greater than total marks.";
    } else {
        try {
            // Calculate percentage
            $percentage = ($marksObtained / $totalMarks) * 100;
            
            // Update result
            $stmt = $pdo->prepare("UPDATE results SET student_id = ?, subject_id = ?, exam_name = ?, exam_date = ?, marks_obtained = ?, total_marks = ?, grade = ?, percentage = ?, remarks = ? WHERE id = ?");
            $stmt->execute([$studentId, $subjectId, $examName, $examDate, $marksObtained, $totalMarks, $grade, $percentage, $remarks, $resultId]);
            
            $message = "Result updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle result deletion
if (isset($_POST['delete_result'])) {
    $resultId = (int)$_POST['result_id'];
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // First check if result exists and get student info
        $checkStmt = $pdo->prepare("SELECT r.exam_name, s.full_name as student_name FROM results r LEFT JOIN students s ON r.student_id = s.id WHERE r.id = ?");
        $checkStmt->execute([$resultId]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            throw new Exception("Result not found.");
        }
        
        // Delete result
        $stmt = $pdo->prepare("DELETE FROM results WHERE id = ?");
        $stmt->execute([$resultId]);
        
        // Commit transaction
        $pdo->commit();
        
        $message = "Result for exam '" . $result['exam_name'] . "' for student '" . $result['student_name'] . "' deleted successfully.";
    } catch (Exception $e) {
        // Rollback transaction
        $pdo->rollBack();
        $error = "Error deleting result: " . $e->getMessage();
    } catch (PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        $error = "Database error: " . $e->getMessage();
    }
}

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

// Fetch all results for display
try {
    $stmt = $pdo->query("SELECT r.id, r.student_id, r.subject_id, r.exam_name, r.exam_date, r.marks_obtained, r.total_marks, r.grade, r.percentage, r.created_at, s.full_name as student_name, s.grade_level, sub.subject_name FROM results r LEFT JOIN students s ON r.student_id = s.id LEFT JOIN subjects sub ON r.subject_id = sub.id ORDER BY r.created_at DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $results = [];
}

// Fetch students for dropdown with class information
try {
    $stmt = $pdo->query("SELECT id, student_id, full_name, grade_level FROM students ORDER BY grade_level, full_name");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $students = [];
}

// Fetch subjects for dropdown
try {
    $stmt = $pdo->query("SELECT id, subject_name, subject_code FROM subjects ORDER BY subject_name");
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $subjects = [];
}

// Get user info from session
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results Management - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .editor-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .editor-header h1 {
            margin: 0;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .results-table th,
        .results-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .results-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }
        
        .results-table tr:last-child td {
            border-bottom: none;
        }
        
        .results-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-small {
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 10px;
            width: 90%;
            max-width: 700px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-height: 80vh;
            overflow-y: auto;
        }
        
        #deleteResultModal .modal-content {
            border-left: 5px solid #dc3545;
        }
        
        #deleteResultModal .modal-body ul {
            padding-left: 20px;
            margin: 10px 0;
        }
        
        #deleteResultModal .modal-body li {
            margin-bottom: 5px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h2 {
            margin: 0;
            color: #333;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }
        
        .close-modal:hover {
            color: #333;
        }
        
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
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-col {
            flex: 1;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
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
        
        .grade-display {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .grade-a { background-color: #4CAF50; color: white; }
        .grade-b { background-color: #2196F3; color: white; }
        .grade-c { background-color: #FFC107; color: black; }
        .grade-d { background-color: #FF9800; color: white; }
        .grade-f { background-color: #F44336; color: white; }
        
        .badge-secondary {
            background-color: #6c757d;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .editor-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .results-table {
                font-size: 0.9rem;
            }
            
            .results-table th,
            .results-table td {
                padding: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn-small {
                width: 100%;
                text-align: center;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .modal-content {
                max-width: 95%;
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
                    <a href="admin-dashboard.php"><i class="fas fa-user-shield"></i> Admin Panel</a>
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

        
    </header>

    <section class="admin-header">
        <div class="container">
            <h1><i class="fas fa-clipboard-list"></i> Results Management</h1>
            <p>Manage student grades and results</p>
        </div>
    </section>

    <section class="admin-content">
        <div class="editor-container">
            <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <div class="editor-header">
                <h1>Results Records</h1>
                <div>
                    <button id="showCreateModal" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Result
                    </button>
                </div>
            </div>
            
            <table class="results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Subject</th>
                        <th>Exam</th>
                        <th>Exam Date</th>
                        <th>Marks</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($result['id']); ?></td>
                        <td>
                                                    <?php 
                                                        $studentDisplay = htmlspecialchars($result['student_name'] ?? $result['student_id']);
                                                        if (!empty($result['grade_level'])) {
                                                            $grade = htmlspecialchars($result['grade_level']);
                                                            if (strpos($grade, 'Computer Science') !== false) {
                                                                $studentDisplay .= ' (+2 CS)';
                                                            } elseif (strpos($grade, 'Hotel Management') !== false) {
                                                                $studentDisplay .= ' (+2 HM)';
                                                            } else {
                                                                $studentDisplay .= ' (' . $grade . ')';
                                                            }
                                                        }
                                                        echo $studentDisplay;
                                                    ?>
                                                </td>
                        <td><?php echo htmlspecialchars($result['subject_name'] ?? $result['subject_id']); ?></td>
                        <td><?php echo htmlspecialchars($result['exam_name']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($result['exam_date'])); ?></td>
                        <td><?php echo htmlspecialchars($result['marks_obtained']) . '/' . htmlspecialchars($result['total_marks']); ?></td>
                        <td><?php echo number_format($result['percentage'], 2); ?>%</td>
                        <td>
                            <?php if (!empty($result['grade'])): ?>
                                <span class="grade-display grade-<?php echo strtolower($result['grade']); ?>">
                                    <?php echo htmlspecialchars($result['grade']); ?>
                                </span>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-secondary">Not Available</span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($result['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-outline btn-small edit-result" 
                                        data-id="<?php echo $result['id']; ?>"
                                        data-student-id="<?php echo htmlspecialchars($result['student_id']); ?>"
                                        data-subject-id="<?php echo htmlspecialchars($result['subject_id']); ?>"
                                        data-exam-name="<?php echo htmlspecialchars($result['exam_name']); ?>"
                                        data-exam-date="<?php echo htmlspecialchars($result['exam_date']); ?>"
                                        data-marks-obtained="<?php echo htmlspecialchars($result['marks_obtained']); ?>"
                                        data-total-marks="<?php echo htmlspecialchars($result['total_marks']); ?>"
                                        data-grade="<?php echo htmlspecialchars($result['grade'] ?? ''); ?>"
                                        data-remarks="<?php echo htmlspecialchars($result['remarks'] ?? ''); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline btn-small delete-result" 
                                        data-id="<?php echo $result['id']; ?>"
                                        data-exam-name="<?php echo htmlspecialchars($result['exam_name']); ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create Result Modal -->
    <div id="createResultModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-clipboard-list"></i> Add New Result</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="createResultForm" action="" method="POST">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_student_id">Student *</label>
                            <select id="create_student_id" name="student_id" class="form-control" required>
                                <option value="">Select Student</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo htmlspecialchars($student['id']); ?>">
                                        <?php 
                                            $displayText = htmlspecialchars($student['full_name']) . ' (' . htmlspecialchars($student['student_id']) . ')';
                                            if (!empty($student['grade_level'])) {
                                                $grade = htmlspecialchars($student['grade_level']);
                                                // Highlight the +2 programs
                                                if (strpos($grade, 'Computer Science') !== false) {
                                                    $displayText .= ' - Class: +2 Computer Science';
                                                } elseif (strpos($grade, 'Hotel Management') !== false) {
                                                    $displayText .= ' - Class: +2 Hotel Management';
                                                } else {
                                                    $displayText .= ' - Class: ' . $grade;
                                                }
                                            }
                                            echo $displayText;
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_subject_id">Subject *</label>
                            <select id="create_subject_id" name="subject_id" class="form-control" required>
                                <option value="">Select Subject</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo htmlspecialchars($subject['id']); ?>">
                                        <?php echo htmlspecialchars($subject['subject_name']) . ' (' . htmlspecialchars($subject['subject_code']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_exam_name">Exam Name *</label>
                            <input type="text" id="create_exam_name" name="exam_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_exam_date">Exam Date *</label>
                            <input type="date" id="create_exam_date" name="exam_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_marks_obtained">Marks Obtained *</label>
                            <input type="number" id="create_marks_obtained" name="marks_obtained" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_total_marks">Total Marks *</label>
                            <input type="number" id="create_total_marks" name="total_marks" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_grade">Grade</label>
                            <input type="text" id="create_grade" name="grade" class="form-control">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_remarks">Remarks</label>
                            <input type="text" id="create_remarks" name="remarks" class="form-control">
                        </div>
                    </div>
                </div>
                
                <!-- Certificate upload removed as certificate_path column doesn't exist in database -->
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="create_result" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Result
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Result Modal -->
    <div id="editResultModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Result</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="editResultForm" action="" method="POST">
                <input type="hidden" id="edit_result_id" name="result_id">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_student_id">Student *</label>
                            <select id="edit_student_id" name="student_id" class="form-control" required>
                                <option value="">Select Student</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo htmlspecialchars($student['id']); ?>">
                                        <?php 
                                            $displayText = htmlspecialchars($student['full_name']) . ' (' . htmlspecialchars($student['student_id']) . ')';
                                            if (!empty($student['grade_level'])) {
                                                $grade = htmlspecialchars($student['grade_level']);
                                                // Highlight the +2 programs
                                                if (strpos($grade, 'Computer Science') !== false) {
                                                    $displayText .= ' - Class: +2 Computer Science';
                                                } elseif (strpos($grade, 'Hotel Management') !== false) {
                                                    $displayText .= ' - Class: +2 Hotel Management';
                                                } else {
                                                    $displayText .= ' - Class: ' . $grade;
                                                }
                                            }
                                            echo $displayText;
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_subject_id">Subject *</label>
                            <select id="edit_subject_id" name="subject_id" class="form-control" required>
                                <option value="">Select Subject</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo htmlspecialchars($subject['id']); ?>">
                                        <?php echo htmlspecialchars($subject['subject_name']) . ' (' . htmlspecialchars($subject['subject_code']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_exam_name">Exam Name *</label>
                            <input type="text" id="edit_exam_name" name="exam_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_exam_date">Exam Date *</label>
                            <input type="date" id="edit_exam_date" name="exam_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_marks_obtained">Marks Obtained *</label>
                            <input type="number" id="edit_marks_obtained" name="marks_obtained" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_total_marks">Total Marks *</label>
                            <input type="number" id="edit_total_marks" name="total_marks" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_grade">Grade</label>
                            <input type="text" id="edit_grade" name="grade" class="form-control">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_remarks">Remarks</label>
                            <input type="text" id="edit_remarks" name="remarks" class="form-control">
                        </div>
                    </div>
                </div>
                
                <!-- Certificate upload removed as certificate_path column doesn't exist in database -->
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="update_result" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Result
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteResultModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>You are about to permanently delete the result for exam <strong id="deleteResultExamName"></strong>.</p>
                <p><strong>Warning:</strong> This action will:</p>
                <ul>
                    <li>Permanently remove this result record</li>
                    <li>Cannot be undone</li>
                </ul>
                <p>Are you sure you want to proceed?</p>
            </div>
            <form id="deleteResultForm" action="" method="POST">
                <input type="hidden" id="delete_result_id" name="result_id">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="delete_result" class="btn btn-primary" style="background-color: #dc3545;">
                        <i class="fas fa-trash"></i> Yes, Delete Result
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Student Modal -->
    <div id="deleteStudentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Student Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>You are about to permanently delete a student and all their associated results.</p>
                <div class="form-group">
                    <label for="delete_student_id_select">Select Student to Delete:</label>
                    <select id="delete_student_id_select" name="student_id" class="form-control" required>
                        <option value="">Select Student</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo htmlspecialchars($student['id']); ?>">
                                <?php 
                                    $displayText = htmlspecialchars($student['full_name']) . ' (' . htmlspecialchars($student['student_id']) . ')';
                                    if (!empty($student['grade_level'])) {
                                        $grade = htmlspecialchars($student['grade_level']);
                                        if (strpos($grade, 'Computer Science') !== false) {
                                            $displayText .= ' - +2 CS';
                                        } elseif (strpos($grade, 'Hotel Management') !== false) {
                                            $displayText .= ' - +2 HM';
                                        } else {
                                            $displayText .= ' - ' . $grade;
                                        }
                                    }
                                    echo $displayText;
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <p><strong>Warning:</strong> This action will:</p>
                <ul>
                    <li>Permanently remove all student information</li>
                    <li>Delete all associated results for this student</li>
                    <li>Cannot be undone</li>
                </ul>
                <p>Are you sure you want to proceed?</p>
            </div>
            <form id="deleteStudentForm" action="" method="POST">
                <input type="hidden" id="delete_student_id_hidden" name="student_id">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="delete_student_with_results" class="btn btn-primary" style="background-color: #dc3545;">
                        <i class="fas fa-trash"></i> Yes, Delete Student
                    </button>
                </div>
            </form>
        </div>
    </div>

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

    <script>
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Get modal elements
            const createResultModal = document.getElementById('createResultModal');
            const editResultModal = document.getElementById('editResultModal');
            const deleteResultModal = document.getElementById('deleteResultModal');
            const deleteStudentModal = document.getElementById('deleteStudentModal');
            
            // Get buttons
            const showCreateModalBtn = document.getElementById('showCreateModal');
            const showStudentDeleteModalBtn = document.getElementById('showStudentDeleteModal');
            const editResultButtons = document.querySelectorAll('.edit-result');
            const deleteResultButtons = document.querySelectorAll('.delete-result');
            const closeModalButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            
            // Show create result modal
            showCreateModalBtn.addEventListener('click', function() {
                createResultModal.style.display = 'flex';
            });
            
            // Show student delete modal
            showStudentDeleteModalBtn.addEventListener('click', function() {
                deleteStudentModal.style.display = 'flex';
            });
            
            // Show edit result modal
            editResultButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const resultId = this.getAttribute('data-id');
                    const studentId = this.getAttribute('data-student-id');
                    const subjectId = this.getAttribute('data-subject-id');
                    const examName = this.getAttribute('data-exam-name');
                    const examDate = this.getAttribute('data-exam-date');
                    const marksObtained = this.getAttribute('data-marks-obtained');
                    const totalMarks = this.getAttribute('data-total-marks');
                    const grade = this.getAttribute('data-grade');
                    const remarks = this.getAttribute('data-remarks');
                    
                    // Set form values
                    document.getElementById('edit_result_id').value = resultId;
                    document.getElementById('edit_student_id').value = studentId;
                    document.getElementById('edit_subject_id').value = subjectId;
                    document.getElementById('edit_exam_name').value = examName;
                    document.getElementById('edit_exam_date').value = examDate;
                    document.getElementById('edit_marks_obtained').value = marksObtained;
                    document.getElementById('edit_total_marks').value = totalMarks;
                    document.getElementById('edit_grade').value = grade;
                    document.getElementById('edit_remarks').value = remarks;
                    
                    // Certificate display removed as certificate_path column doesn't exist in database
                    
                    editResultModal.style.display = 'flex';
                });
            });
            
            // Show delete confirmation modal
            deleteResultButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const resultId = this.getAttribute('data-id');
                    const examName = this.getAttribute('data-exam-name');
                    
                    document.getElementById('delete_result_id').value = resultId;
                    document.getElementById('deleteResultExamName').textContent = examName;
                    deleteResultModal.style.display = 'flex';
                });
            });
            
            // Handle student selection for deletion
            document.getElementById('delete_student_id_select').addEventListener('change', function() {
                document.getElementById('delete_student_id_hidden').value = this.value;
            });
            
            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    createResultModal.style.display = 'none';
                    editResultModal.style.display = 'none';
                    deleteResultModal.style.display = 'none';
                    deleteStudentModal.style.display = 'none';
                });
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === createResultModal) {
                    createResultModal.style.display = 'none';
                }
                if (event.target === editResultModal) {
                    editResultModal.style.display = 'none';
                }
                if (event.target === deleteResultModal) {
                    deleteResultModal.style.display = 'none';
                }
                if (event.target === deleteStudentModal) {
                    deleteStudentModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>