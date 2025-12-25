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

// Handle course creation
if (isset($_POST['create_course'])) {
    $subjectName = trim($_POST['subject_name']);
    $subjectCode = trim($_POST['subject_code']);
    $description = trim($_POST['description']);
    $credits = (int)$_POST['credits'];
    $department = trim($_POST['department']);
    
    // Validate input
    if (empty($subjectName) || empty($subjectCode)) {
        $error = "Subject name and code are required.";
    } else {
        try {
            // Check if course already exists
            $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_code = ?");
            $stmt->execute([$subjectCode]);
            if ($stmt->fetch()) {
                $error = "A course with this code already exists.";
            } else {
                // Create new course
                $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, subject_code, description, credits, department) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$subjectName, $subjectCode, $description, $credits, $department]);
                
                $message = "Course created successfully.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle course updates
if (isset($_POST['update_course'])) {
    $courseId = (int)$_POST['course_id'];
    $subjectName = trim($_POST['subject_name']);
    $subjectCode = trim($_POST['subject_code']);
    $description = trim($_POST['description']);
    $credits = (int)$_POST['credits'];
    $department = trim($_POST['department']);
    
    // Validate input
    if (empty($subjectName) || empty($subjectCode)) {
        $error = "Subject name and code are required.";
    } else {
        try {
            // Update course
            $stmt = $pdo->prepare("UPDATE subjects SET subject_name = ?, subject_code = ?, description = ?, credits = ?, department = ? WHERE id = ?");
            $stmt->execute([$subjectName, $subjectCode, $description, $credits, $department, $courseId]);
            
            $message = "Course updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle course deletion
if (isset($_POST['delete_course'])) {
    $courseId = (int)$_POST['course_id'];
    
    try {
        // Delete course
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$courseId]);
        
        $message = "Course deleted successfully.";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch all courses for display
try {
    $stmt = $pdo->query("SELECT id, subject_name, subject_code, description, credits, department, created_at FROM subjects ORDER BY created_at DESC");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $courses = [];
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
    <title>Course Management - Excellence School</title>
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
        
        .course-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .course-table th,
        .course-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .course-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }
        
        .course-table tr:last-child td {
            border-bottom: none;
        }
        
        .course-table tr:hover {
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
            
            .course-table {
                font-size: 0.9rem;
            }
            
            .course-table th,
            .course-table td {
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
                            <p class="tagline"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="admin-header">
        <div class="container">
            <h1><i class="fas fa-book-open"></i> Course Management</h1>
            <p>Manage courses and curriculum</p>
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
                <h1>Course Records</h1>
                <button id="showCreateModal" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Course
                </button>
            </div>
            
            <table class="course-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Description</th>
                        <th>Credits</th>
                        <th>Department</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['id']); ?></td>
                        <td><?php echo htmlspecialchars($course['subject_code']); ?></td>
                        <td><?php echo htmlspecialchars($course['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars(substr($course['description'], 0, 50)) . (strlen($course['description']) > 50 ? '...' : ''); ?></td>
                        <td><?php echo htmlspecialchars($course['credits']); ?></td>
                        <td><?php echo htmlspecialchars($course['department']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($course['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-outline btn-small edit-course" 
                                        data-id="<?php echo $course['id']; ?>"
                                        data-code="<?php echo htmlspecialchars($course['subject_code']); ?>"
                                        data-name="<?php echo htmlspecialchars($course['subject_name']); ?>"
                                        data-description="<?php echo htmlspecialchars($course['description']); ?>"
                                        data-credits="<?php echo htmlspecialchars($course['credits']); ?>"
                                        data-department="<?php echo htmlspecialchars($course['department']); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline btn-small delete-course" 
                                        data-id="<?php echo $course['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($course['subject_name']); ?>">
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

    <!-- Create Course Modal -->
    <div id="createCourseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-book-open"></i> Add New Course</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="createCourseForm" action="" method="POST">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_subject_code">Course Code *</label>
                            <input type="text" id="create_subject_code" name="subject_code" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_subject_name">Course Name *</label>
                            <input type="text" id="create_subject_name" name="subject_name" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="create_description">Description</label>
                    <textarea id="create_description" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_credits">Credits</label>
                            <input type="number" id="create_credits" name="credits" class="form-control" min="0" max="10">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_department">Department</label>
                            <input type="text" id="create_department" name="department" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="create_course" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Course
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div id="editCourseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Course</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="editCourseForm" action="" method="POST">
                <input type="hidden" id="edit_course_id" name="course_id">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_subject_code">Course Code *</label>
                            <input type="text" id="edit_subject_code" name="subject_code" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_subject_name">Course Name *</label>
                            <input type="text" id="edit_subject_name" name="subject_name" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_credits">Credits</label>
                            <input type="number" id="edit_credits" name="credits" class="form-control" min="0" max="10">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_department">Department</label>
                            <input type="text" id="edit_department" name="department" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="update_course" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Course
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteCourseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete course <strong id="deleteCourseName"></strong>? This action cannot be undone.</p>
            </div>
            <form id="deleteCourseForm" action="" method="POST">
                <input type="hidden" id="delete_course_id" name="course_id">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="delete_course" class="btn btn-primary" style="background-color: #dc3545;">
                        <i class="fas fa-trash"></i> Delete Course
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
            const createCourseModal = document.getElementById('createCourseModal');
            const editCourseModal = document.getElementById('editCourseModal');
            const deleteCourseModal = document.getElementById('deleteCourseModal');
            
            // Get buttons
            const showCreateModalBtn = document.getElementById('showCreateModal');
            const editCourseButtons = document.querySelectorAll('.edit-course');
            const deleteCourseButtons = document.querySelectorAll('.delete-course');
            const closeModalButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            
            // Show create course modal
            showCreateModalBtn.addEventListener('click', function() {
                createCourseModal.style.display = 'flex';
            });
            
            // Show edit course modal
            editCourseButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.getAttribute('data-id');
                    const courseCode = this.getAttribute('data-code');
                    const courseName = this.getAttribute('data-name');
                    const courseDescription = this.getAttribute('data-description');
                    const courseCredits = this.getAttribute('data-credits');
                    const courseDepartment = this.getAttribute('data-department');
                    
                    document.getElementById('edit_course_id').value = courseId;
                    document.getElementById('edit_subject_code').value = courseCode;
                    document.getElementById('edit_subject_name').value = courseName;
                    document.getElementById('edit_description').value = courseDescription;
                    document.getElementById('edit_credits').value = courseCredits;
                    document.getElementById('edit_department').value = courseDepartment;
                    
                    editCourseModal.style.display = 'flex';
                });
            });
            
            // Show delete confirmation modal
            deleteCourseButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.getAttribute('data-id');
                    const courseName = this.getAttribute('data-name');
                    
                    document.getElementById('delete_course_id').value = courseId;
                    document.getElementById('deleteCourseName').textContent = courseName;
                    deleteCourseModal.style.display = 'flex';
                });
            });
            
            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    createCourseModal.style.display = 'none';
                    editCourseModal.style.display = 'none';
                    deleteCourseModal.style.display = 'none';
                });
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === createCourseModal) {
                    createCourseModal.style.display = 'none';
                }
                if (event.target === editCourseModal) {
                    editCourseModal.style.display = 'none';
                }
                if (event.target === deleteCourseModal) {
                    deleteCourseModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>