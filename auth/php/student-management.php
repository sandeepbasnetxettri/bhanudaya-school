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

// Handle student creation
if (isset($_POST['create_student'])) {
    $studentId = trim($_POST['student_id']);
    $fullName = trim($_POST['full_name']);
    $dateOfBirth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $gradeLevel = trim($_POST['grade_level']);
    $parentGuardianName = trim($_POST['parent_guardian_name']);
    
    // Validate input
    if (empty($studentId) || empty($fullName) || empty($dateOfBirth) || empty($gender) || empty($gradeLevel)) {
        $error = "Required fields are missing.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Check if student already exists
            $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
            $stmt->execute([$studentId]);
            if ($stmt->fetch()) {
                $error = "A student with this ID already exists.";
            } else {
                // Create new student
                $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, date_of_birth, gender, address, phone, email, enrollment_date, grade_level, parent_guardian_name) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
                $stmt->execute([$studentId, $fullName, $dateOfBirth, $gender, $address, $phone, $email, $gradeLevel, $parentGuardianName]);
                
                $message = "Student created successfully.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle student updates
if (isset($_POST['update_student'])) {
    $studentId = (int)$_POST['student_id'];
    $studentUniqueId = trim($_POST['student_unique_id']);
    $fullName = trim($_POST['full_name']);
    $dateOfBirth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $gradeLevel = trim($_POST['grade_level']);
    $parentGuardianName = trim($_POST['parent_guardian_name']);
    
    // Validate input
    if (empty($studentUniqueId) || empty($fullName) || empty($dateOfBirth) || empty($gender) || empty($gradeLevel)) {
        $error = "Required fields are missing.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Update student
            $stmt = $pdo->prepare("UPDATE students SET student_id = ?, full_name = ?, date_of_birth = ?, gender = ?, address = ?, phone = ?, email = ?, grade_level = ?, parent_guardian_name = ? WHERE id = ?");
            $stmt->execute([$studentUniqueId, $fullName, $dateOfBirth, $gender, $address, $phone, $email, $gradeLevel, $parentGuardianName, $studentId]);
            
            $message = "Student updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

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

// Fetch all students for display
try {
    $stmt = $pdo->query("SELECT id, student_id, full_name, date_of_birth, gender, grade_level, enrollment_date FROM students ORDER BY enrollment_date DESC");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $students = [];
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
    <title>Student Management - Excellence School</title>
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
        
        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .student-table th,
        .student-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .student-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }
        
        .student-table tr:last-child td {
            border-bottom: none;
        }
        
        .student-table tr:hover {
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
            max-width: 600px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-height: 80vh;
            overflow-y: auto;
        }
        
        #deleteStudentModal .modal-content {
            border-left: 5px solid #dc3545;
        }
        
        #deleteStudentModal .modal-body ul {
            padding-left: 20px;
            margin: 10px 0;
        }
        
        #deleteStudentModal .modal-body li {
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
        
        @media (max-width: 768px) {
            .editor-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .student-table {
                font-size: 0.9rem;
            }
            
            .student-table th,
            .student-table td {
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
                            <p class="tagline">Building Tomorrow's Leaders Today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="admin-header">
        <div class="container">
            <h1><i class="fas fa-user-graduate"></i> Student Management</h1>
            <p>Manage student records and information</p>
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
                <h1>Student Records</h1>
                <button id="showCreateModal" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Student
                </button>
            </div>
            
            <table class="student-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Grade Level</th>
                        <th>Enrollment Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($student['date_of_birth'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($student['gender'])); ?></td>
                        <td><?php echo htmlspecialchars($student['grade_level']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($student['enrollment_date'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-outline btn-small edit-student" 
                                        data-id="<?php echo $student['id']; ?>"
                                        data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>"
                                        data-name="<?php echo htmlspecialchars($student['full_name']); ?>"
                                        data-dob="<?php echo htmlspecialchars($student['date_of_birth']); ?>"
                                        data-gender="<?php echo htmlspecialchars($student['gender']); ?>"
                                        data-grade="<?php echo htmlspecialchars($student['grade_level']); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline btn-small delete-student" 
                                        data-id="<?php echo $student['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($student['full_name']); ?>">
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

    <!-- Create Student Modal -->
    <div id="createStudentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-graduate"></i> Add New Student</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="createStudentForm" action="" method="POST">
                <div class="form-group">
                    <label for="create_student_id">Student ID *</label>
                    <input type="text" id="create_student_id" name="student_id" class="form-control" required>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_full_name">Full Name *</label>
                            <input type="text" id="create_full_name" name="full_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_date_of_birth">Date of Birth *</label>
                            <input type="date" id="create_date_of_birth" name="date_of_birth" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_gender">Gender *</label>
                            <select id="create_gender" name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                                <option value="prefer_not_to_say">Prefer not to say</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_grade_level">Grade Level *</label>
                            <input type="text" id="create_grade_level" name="grade_level" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="create_address">Address</label>
                    <textarea id="create_address" name="address" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_phone">Phone</label>
                            <input type="text" id="create_phone" name="phone" class="form-control">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_email">Email</label>
                            <input type="email" id="create_email" name="email" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="create_parent_guardian_name">Parent/Guardian Name</label>
                    <input type="text" id="create_parent_guardian_name" name="parent_guardian_name" class="form-control">
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="create_student" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div id="editStudentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Student</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="editStudentForm" action="" method="POST">
                <input type="hidden" id="edit_student_id" name="student_id">
                <div class="form-group">
                    <label for="edit_student_unique_id">Student ID *</label>
                    <input type="text" id="edit_student_unique_id" name="student_unique_id" class="form-control" required>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_full_name">Full Name *</label>
                            <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_date_of_birth">Date of Birth *</label>
                            <input type="date" id="edit_date_of_birth" name="date_of_birth" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_gender">Gender *</label>
                            <select id="edit_gender" name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                                <option value="prefer_not_to_say">Prefer not to say</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_grade_level">Grade Level *</label>
                            <input type="text" id="edit_grade_level" name="grade_level" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_address">Address</label>
                    <textarea id="edit_address" name="address" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_phone">Phone</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_email">Email</label>
                            <input type="email" id="edit_email" name="email" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_parent_guardian_name">Parent/Guardian Name</label>
                    <input type="text" id="edit_parent_guardian_name" name="parent_guardian_name" class="form-control">
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="update_student" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteStudentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>You are about to permanently delete student <strong id="deleteStudentName"></strong>.</p>
                <p><strong>Warning:</strong> This action will:</p>
                <ul>
                    <li>Permanently remove all student information</li>
                    <li>Delete all associated results and records</li>
                    <li>Cannot be undone</li>
                </ul>
                <p>Are you sure you want to proceed?</p>
            </div>
            <form id="deleteStudentForm" action="" method="POST">
                <input type="hidden" id="delete_student_id" name="student_id">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="delete_student" class="btn btn-primary" style="background-color: #dc3545;">
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
            const createStudentModal = document.getElementById('createStudentModal');
            const editStudentModal = document.getElementById('editStudentModal');
            const deleteStudentModal = document.getElementById('deleteStudentModal');
            
            // Get buttons
            const showCreateModalBtn = document.getElementById('showCreateModal');
            const editStudentButtons = document.querySelectorAll('.edit-student');
            const deleteStudentButtons = document.querySelectorAll('.delete-student');
            const closeModalButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            
            // Show create student modal
            showCreateModalBtn.addEventListener('click', function() {
                createStudentModal.style.display = 'flex';
            });
            
            // Show edit student modal
            editStudentButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-id');
                    const studentUniqueId = this.getAttribute('data-student-id');
                    const studentName = this.getAttribute('data-name');
                    const studentDob = this.getAttribute('data-dob');
                    const studentGender = this.getAttribute('data-gender');
                    const studentGrade = this.getAttribute('data-grade');
                    
                    document.getElementById('edit_student_id').value = studentId;
                    document.getElementById('edit_student_unique_id').value = studentUniqueId;
                    document.getElementById('edit_full_name').value = studentName;
                    document.getElementById('edit_date_of_birth').value = studentDob;
                    document.getElementById('edit_gender').value = studentGender;
                    document.getElementById('edit_grade_level').value = studentGrade;
                    
                    editStudentModal.style.display = 'flex';
                });
            });
            
            // Show delete confirmation modal
            deleteStudentButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-id');
                    const studentName = this.getAttribute('data-name');
                    
                    document.getElementById('delete_student_id').value = studentId;
                    document.getElementById('deleteStudentName').textContent = studentName;
                    deleteStudentModal.style.display = 'flex';
                });
            });
            
            // Add confirmation before deletion
            document.getElementById('deleteStudentForm').addEventListener('submit', function(e) {
                const studentName = document.getElementById('deleteStudentName').textContent;
                if (!confirm(`Are you absolutely sure you want to delete student ${studentName}? This action cannot be undone.`)) {
                    e.preventDefault();
                }
            });
            
            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    createStudentModal.style.display = 'none';
                    editStudentModal.style.display = 'none';
                    deleteStudentModal.style.display = 'none';
                });
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === createStudentModal) {
                    createStudentModal.style.display = 'none';
                }
                if (event.target === editStudentModal) {
                    editStudentModal.style.display = 'none';
                }
                if (event.target === deleteStudentModal) {
                    deleteStudentModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>