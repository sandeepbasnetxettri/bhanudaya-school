<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

// Database connection
require_once '../../config/dbconnection.php';

$userName = $_SESSION['user_name'];
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

// Handle homework creation
$message = '';
$error = '';

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
                $error = "Failed to upload attachment file.";
            }
        } else {
            $error = "Invalid file type. Only PDF, DOC, DOCX, JPG, JPEG, and PNG files are allowed.";
        }
    }
    
    // Validate input
    if (empty($title) || empty($subjectId) || empty($classId) || empty($dueDate)) {
        $error = "Title, subject, class, and due date are required.";
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
            
            $message = "Homework created successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle homework deletion
if (isset($_POST['delete_homework'])) {
    $assignmentId = (int)$_POST['assignment_id'];
    
    try {
        // Delete assignment
        $stmt = $pdo->prepare("DELETE FROM assignments WHERE id = ? AND assigned_by = ?");
        $stmt->execute([$assignmentId, $teacherId]);
        
        $message = "Homework deleted successfully.";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Get classes for dropdown
try {
    $stmt = $pdo->prepare("SELECT c.id, c.class_name, s.subject_name, s.id as subject_id FROM classes c JOIN subjects s ON c.id = s.id WHERE c.teacher_id = ?");
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

// Get all assignments for this teacher
try {
    $stmt = $pdo->prepare("SELECT a.*, c.class_name, s.subject_name FROM assignments a JOIN classes c ON a.class_id = c.id JOIN subjects s ON a.subject_id = s.id WHERE a.assigned_by = ? ORDER BY a.created_at DESC");
    $stmt->execute([$teacherId]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $assignments = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homework Management - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/portal.css">
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
        
        .assignments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .assignments-table th,
        .assignments-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .assignments-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }
        
        .assignments-table tr:last-child td {
            border-bottom: none;
        }
        
        .assignments-table tr:hover {
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
        
        .file-input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        @media (max-width: 768px) {
            .editor-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .assignments-table {
                font-size: 0.9rem;
            }
            
            .assignments-table th,
            .assignments-table td {
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
                    <a href="teacher-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
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
    </header>

    <section class="portal-header">
        <div class="container">
            <h1><i class="fas fa-book"></i> Homework Management</h1>
            <p>Manage your homework assignments and attachments</p>
        </div>
    </section>

    <section class="portal-content">
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
                <h1>Your Homework Assignments</h1>
                <button id="showCreateModal" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Homework
                </button>
            </div>
            
            <table class="assignments-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Subject & Class</th>
                        <th>Due Date</th>
                        <th>Created</th>
                        <th>Attachment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assignments)): ?>
                        <?php foreach ($assignments as $assignment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                            <td><?php echo htmlspecialchars($assignment['subject_name']); ?> - <?php echo htmlspecialchars($assignment['class_name']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($assignment['due_date'])); ?></td>
                            <td><?php echo date('M j, Y', strtotime($assignment['created_at'])); ?></td>
                            <td>
                                <?php if (!empty($assignment['attachment_url'])): ?>
                                    <a href="../../<?php echo htmlspecialchars($assignment['attachment_url']); ?>" target="_blank" class="btn btn-outline btn-small">
                                        <i class="fas fa-download"></i> View
                                    </a>
                                <?php else: ?>
                                    <span class="badge badge-secondary">None</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-outline btn-small delete-homework" 
                                            data-id="<?php echo $assignment['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($assignment['title']); ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No homework assignments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create Homework Modal -->
    <div id="createHomeworkModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-book"></i> Create New Homework</h2>
                <button class="close-modal">&times;</button>
            </div>
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
                    <input type="file" id="homework_attachment" name="homework_attachment" class="file-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="create_homework" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Assign Homework
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteHomeworkModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete homework assignment <strong id="deleteHomeworkTitle"></strong>? This action cannot be undone.</p>
            </div>
            <form id="deleteHomeworkForm" action="" method="POST">
                <input type="hidden" id="delete_assignment_id" name="assignment_id">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="delete_homework" class="btn btn-primary" style="background-color: #dc3545;">
                        <i class="fas fa-trash"></i> Delete Homework
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
            const createHomeworkModal = document.getElementById('createHomeworkModal');
            const deleteHomeworkModal = document.getElementById('deleteHomeworkModal');
            
            // Get buttons
            const showCreateModalBtn = document.getElementById('showCreateModal');
            const deleteHomeworkButtons = document.querySelectorAll('.delete-homework');
            const closeModalButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            
            // Show create homework modal
            showCreateModalBtn.addEventListener('click', function() {
                createHomeworkModal.style.display = 'flex';
            });
            
            // Show delete confirmation modal
            deleteHomeworkButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const assignmentId = this.getAttribute('data-id');
                    const assignmentTitle = this.getAttribute('data-title');
                    
                    document.getElementById('delete_assignment_id').value = assignmentId;
                    document.getElementById('deleteHomeworkTitle').textContent = assignmentTitle;
                    deleteHomeworkModal.style.display = 'flex';
                });
            });
            
            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    createHomeworkModal.style.display = 'none';
                    deleteHomeworkModal.style.display = 'none';
                });
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === createHomeworkModal) {
                    createHomeworkModal.style.display = 'none';
                }
                if (event.target === deleteHomeworkModal) {
                    deleteHomeworkModal.style.display = 'none';
                }
            });
            
            // Set min date for due date input to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('due_date').setAttribute('min', today);
        });
    </script>
</body>
</html>