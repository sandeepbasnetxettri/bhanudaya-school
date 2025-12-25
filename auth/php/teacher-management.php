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

// Handle teacher creation
if (isset($_POST['create_teacher'])) {
    $employeeId = trim($_POST['employee_id']);
    $fullName = trim($_POST['full_name']);
    $dateOfBirth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $qualification = trim($_POST['qualification']);
    $department = trim($_POST['department']);
    $hireDate = $_POST['hire_date'];
    
    // Validate input
    if (empty($employeeId) || empty($fullName) || empty($dateOfBirth) || empty($gender) || empty($hireDate)) {
        $error = "Required fields are missing.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Check if teacher already exists
            $stmt = $pdo->prepare("SELECT id FROM teachers WHERE employee_id = ?");
            $stmt->execute([$employeeId]);
            if ($stmt->fetch()) {
                $error = "A teacher with this employee ID already exists.";
            } else {
                // Create new teacher
                $stmt = $pdo->prepare("INSERT INTO teachers (employee_id, full_name, date_of_birth, gender, address, phone, email, qualification, department, hire_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$employeeId, $fullName, $dateOfBirth, $gender, $address, $phone, $email, $qualification, $department, $hireDate]);
                
                $message = "Teacher created successfully.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle teacher updates
if (isset($_POST['update_teacher'])) {
    $teacherId = (int)$_POST['teacher_id'];
    $employeeId = trim($_POST['employee_id']);
    $fullName = trim($_POST['full_name']);
    $dateOfBirth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $qualification = trim($_POST['qualification']);
    $department = trim($_POST['department']);
    $hireDate = $_POST['hire_date'];
    
    // Validate input
    if (empty($employeeId) || empty($fullName) || empty($dateOfBirth) || empty($gender) || empty($hireDate)) {
        $error = "Required fields are missing.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Update teacher
            $stmt = $pdo->prepare("UPDATE teachers SET employee_id = ?, full_name = ?, date_of_birth = ?, gender = ?, address = ?, phone = ?, email = ?, qualification = ?, department = ?, hire_date = ? WHERE id = ?");
            $stmt->execute([$employeeId, $fullName, $dateOfBirth, $gender, $address, $phone, $email, $qualification, $department, $hireDate, $teacherId]);
            
            $message = "Teacher updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle teacher deletion
if (isset($_POST['delete_teacher'])) {
    $teacherId = (int)$_POST['teacher_id'];
    
    try {
        // Delete teacher
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt->execute([$teacherId]);
        
        $message = "Teacher deleted successfully.";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch all teachers for display
try {
    $stmt = $pdo->query("SELECT id, employee_id, full_name, date_of_birth, gender, department, hire_date FROM teachers ORDER BY hire_date DESC");
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $teachers = [];
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
    <title>Teacher Management - Excellence School</title>
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
        
        .teacher-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .teacher-table th,
        .teacher-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .teacher-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }
        
        .teacher-table tr:last-child td {
            border-bottom: none;
        }
        
        .teacher-table tr:hover {
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
            
            .teacher-table {
                font-size: 0.9rem;
            }
            
            .teacher-table th,
            .teacher-table td {
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
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <section class="admin-header">
        <div class="container">
            <h1><i class="fas fa-chalkboard-teacher"></i> Teacher Management</h1>
            <p>Manage teacher profiles and assignments</p>
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
                <h1>Teacher Records</h1>
                <button id="showCreateModal" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Teacher
                </button>
            </div>
            
            <table class="teacher-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Department</th>
                        <th>Hire Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($teacher['id']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['employee_id']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($teacher['date_of_birth'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($teacher['gender'])); ?></td>
                        <td><?php echo htmlspecialchars($teacher['department']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($teacher['hire_date'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-outline btn-small edit-teacher" 
                                        data-id="<?php echo $teacher['id']; ?>"
                                        data-employee-id="<?php echo htmlspecialchars($teacher['employee_id']); ?>"
                                        data-name="<?php echo htmlspecialchars($teacher['full_name']); ?>"
                                        data-dob="<?php echo htmlspecialchars($teacher['date_of_birth']); ?>"
                                        data-gender="<?php echo htmlspecialchars($teacher['gender']); ?>"
                                        data-department="<?php echo htmlspecialchars($teacher['department']); ?>"
                                        data-hire-date="<?php echo htmlspecialchars($teacher['hire_date']); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline btn-small delete-teacher" 
                                        data-id="<?php echo $teacher['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($teacher['full_name']); ?>">
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

    <!-- Create Teacher Modal -->
    <div id="createTeacherModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-chalkboard-teacher"></i> Add New Teacher</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="createTeacherForm" action="" method="POST">
                <div class="form-group">
                    <label for="create_employee_id">Employee ID *</label>
                    <input type="text" id="create_employee_id" name="employee_id" class="form-control" required>
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
                            <label for="create_hire_date">Hire Date *</label>
                            <input type="date" id="create_hire_date" name="hire_date" class="form-control" required>
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
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_qualification">Qualification</label>
                            <input type="text" id="create_qualification" name="qualification" class="form-control">
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
                    <button type="submit" name="create_teacher" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Teacher Modal -->
    <div id="editTeacherModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Teacher</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="editTeacherForm" action="" method="POST">
                <input type="hidden" id="edit_teacher_id" name="teacher_id">
                <div class="form-group">
                    <label for="edit_employee_id">Employee ID *</label>
                    <input type="text" id="edit_employee_id" name="employee_id" class="form-control" required>
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
                            <label for="edit_hire_date">Hire Date *</label>
                            <input type="date" id="edit_hire_date" name="hire_date" class="form-control" required>
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
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_qualification">Qualification</label>
                            <input type="text" id="edit_qualification" name="qualification" class="form-control">
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
                    <button type="submit" name="update_teacher" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteTeacherModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete teacher <strong id="deleteTeacherName"></strong>? This action cannot be undone.</p>
            </div>
            <form id="deleteTeacherForm" action="" method="POST">
                <input type="hidden" id="delete_teacher_id" name="teacher_id">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="delete_teacher" class="btn btn-primary" style="background-color: #dc3545;">
                        <i class="fas fa-trash"></i> Delete Teacher
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
            const createTeacherModal = document.getElementById('createTeacherModal');
            const editTeacherModal = document.getElementById('editTeacherModal');
            const deleteTeacherModal = document.getElementById('deleteTeacherModal');
            
            // Get buttons
            const showCreateModalBtn = document.getElementById('showCreateModal');
            const editTeacherButtons = document.querySelectorAll('.edit-teacher');
            const deleteTeacherButtons = document.querySelectorAll('.delete-teacher');
            const closeModalButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            
            // Show create teacher modal
            showCreateModalBtn.addEventListener('click', function() {
                createTeacherModal.style.display = 'flex';
            });
            
            // Show edit teacher modal
            editTeacherButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const teacherId = this.getAttribute('data-id');
                    const employeeId = this.getAttribute('data-employee-id');
                    const teacherName = this.getAttribute('data-name');
                    const teacherDob = this.getAttribute('data-dob');
                    const teacherGender = this.getAttribute('data-gender');
                    const teacherDepartment = this.getAttribute('data-department');
                    const teacherHireDate = this.getAttribute('data-hire-date');
                    
                    document.getElementById('edit_teacher_id').value = teacherId;
                    document.getElementById('edit_employee_id').value = employeeId;
                    document.getElementById('edit_full_name').value = teacherName;
                    document.getElementById('edit_date_of_birth').value = teacherDob;
                    document.getElementById('edit_gender').value = teacherGender;
                    document.getElementById('edit_department').value = teacherDepartment;
                    document.getElementById('edit_hire_date').value = teacherHireDate;
                    
                    editTeacherModal.style.display = 'flex';
                });
            });
            
            // Show delete confirmation modal
            deleteTeacherButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const teacherId = this.getAttribute('data-id');
                    const teacherName = this.getAttribute('data-name');
                    
                    document.getElementById('delete_teacher_id').value = teacherId;
                    document.getElementById('deleteTeacherName').textContent = teacherName;
                    deleteTeacherModal.style.display = 'flex';
                });
            });
            
            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    createTeacherModal.style.display = 'none';
                    editTeacherModal.style.display = 'none';
                    deleteTeacherModal.style.display = 'none';
                });
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === createTeacherModal) {
                    createTeacherModal.style.display = 'none';
                }
                if (event.target === editTeacherModal) {
                    editTeacherModal.style.display = 'none';
                }
                if (event.target === deleteTeacherModal) {
                    deleteTeacherModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>