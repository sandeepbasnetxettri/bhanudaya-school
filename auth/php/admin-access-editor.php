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

// Handle user creation
if (isset($_POST['create_user'])) {
    $email = trim($_POST['email']);
    $fullName = trim($_POST['full_name']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Validate input
    if (empty($email) || empty($fullName) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        try {
            // Check if user already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "A user with this email already exists.";
            } else {
                // Create new user
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (email, full_name, password_hash, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$email, $fullName, $passwordHash, $role]);
                
                // Create user profile entry
                $userId = $pdo->lastInsertId();
                $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id) VALUES (?)");
                $stmt->execute([$userId]);
                
                $message = "User created successfully.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle user updates
if (isset($_POST['update_user'])) {
    $userId = (int)$_POST['user_id'];
    $email = trim($_POST['email']);
    $fullName = trim($_POST['full_name']);
    $role = $_POST['role'];
    
    // Validate input
    if (empty($email) || empty($fullName) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Update user
            $stmt = $pdo->prepare("UPDATE users SET email = ?, full_name = ?, role = ? WHERE id = ?");
            $stmt->execute([$email, $fullName, $role, $userId]);
            
            $message = "User updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle password reset
if (isset($_POST['reset_password'])) {
    $userId = (int)$_POST['user_id'];
    $newPassword = $_POST['new_password'];
    
    if (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        try {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$passwordHash, $userId]);
            
            $message = "Password reset successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $userId = (int)$_POST['user_id'];
    
    // Prevent deleting the current admin user
    if ($userId == $_SESSION['user_id']) {
        $error = "You cannot delete your own account.";
    } else {
        try {
            // Delete user (cascades to related tables)
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            $message = "User deleted successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch all users for display
try {
    $stmt = $pdo->query("SELECT id, email, full_name, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $users = [];
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
    <title>Admin Access Editor - Excellence School</title>
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
        
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .user-table th,
        .user-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .user-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }
        
        .user-table tr:last-child td {
            border-bottom: none;
        }
        
        .user-table tr:hover {
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
            max-width: 500px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
            
            .user-table {
                font-size: 0.9rem;
            }
            
            .user-table th,
            .user-table td {
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
            <h1><i class="fas fa-user-edit"></i> Admin Access Editor</h1>
            <p>Manage user accounts and permissions</p>
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
                <h1>User Management</h1>
                <button id="showCreateModal" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New User
                </button>
            </div>
            
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo htmlspecialchars($user['role']); ?>">
                                <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-outline btn-small edit-user" 
                                        data-id="<?php echo $user['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($user['full_name']); ?>"
                                        data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                        data-role="<?php echo htmlspecialchars($user['role']); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline btn-small reset-password" 
                                        data-id="<?php echo $user['id']; ?>">
                                    <i class="fas fa-key"></i> Reset Pass
                                </button>
                                <button class="btn btn-outline btn-small delete-user" 
                                        data-id="<?php echo $user['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($user['full_name']); ?>">
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

    <!-- Create User Modal -->
    <div id="createUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Create New User</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="createUserForm" action="" method="POST">
                <div class="form-group">
                    <label for="create_full_name">Full Name</label>
                    <input type="text" id="create_full_name" name="full_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="create_email">Email Address</label>
                    <input type="email" id="create_email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="create_password">Password</label>
                    <input type="password" id="create_password" name="password" class="form-control" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="create_role">Role</label>
                    <select id="create_role" name="role" class="form-control" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                        <option value="parent">Parent</option>
                    </select>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="create_user" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit User</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="editUserForm" action="" method="POST">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="form-group">
                    <label for="edit_full_name">Full Name</label>
                    <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email Address</label>
                    <input type="email" id="edit_email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_role">Role</label>
                    <select id="edit_role" name="role" class="form-control" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                        <option value="parent">Parent</option>
                    </select>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="update_user" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-key"></i> Reset Password</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="resetPasswordForm" action="" method="POST">
                <input type="hidden" id="reset_user_id" name="user_id">
                <div class="form-group">
                    <label for="reset_new_password">New Password</label>
                    <input type="password" id="reset_new_password" name="new_password" class="form-control" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="reset_confirm_password">Confirm Password</label>
                    <input type="password" id="reset_confirm_password" class="form-control" required minlength="6">
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="reset_password" class="btn btn-primary">
                        <i class="fas fa-sync"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>? This action cannot be undone.</p>
            </div>
            <form id="deleteUserForm" action="" method="POST">
                <input type="hidden" id="delete_user_id" name="user_id">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="delete_user" class="btn btn-primary" style="background-color: #dc3545;">
                        <i class="fas fa-trash"></i> Delete User
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
            const createUserModal = document.getElementById('createUserModal');
            const editUserModal = document.getElementById('editUserModal');
            const resetPasswordModal = document.getElementById('resetPasswordModal');
            const deleteUserModal = document.getElementById('deleteUserModal');
            
            // Get buttons
            const showCreateModalBtn = document.getElementById('showCreateModal');
            const editUserButtons = document.querySelectorAll('.edit-user');
            const resetPasswordButtons = document.querySelectorAll('.reset-password');
            const deleteUserButtons = document.querySelectorAll('.delete-user');
            const closeModalButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            
            // Show create user modal
            showCreateModalBtn.addEventListener('click', function() {
                createUserModal.style.display = 'flex';
            });
            
            // Show edit user modal
            editUserButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    const userName = this.getAttribute('data-name');
                    const userEmail = this.getAttribute('data-email');
                    const userRole = this.getAttribute('data-role');
                    
                    document.getElementById('edit_user_id').value = userId;
                    document.getElementById('edit_full_name').value = userName;
                    document.getElementById('edit_email').value = userEmail;
                    document.getElementById('edit_role').value = userRole;
                    
                    editUserModal.style.display = 'flex';
                });
            });
            
            // Show reset password modal
            resetPasswordButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    document.getElementById('reset_user_id').value = userId;
                    resetPasswordModal.style.display = 'flex';
                });
            });
            
            // Show delete confirmation modal
            deleteUserButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    const userName = this.getAttribute('data-name');
                    
                    document.getElementById('delete_user_id').value = userId;
                    document.getElementById('deleteUserName').textContent = userName;
                    deleteUserModal.style.display = 'flex';
                });
            });
            
            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    createUserModal.style.display = 'none';
                    editUserModal.style.display = 'none';
                    resetPasswordModal.style.display = 'none';
                    deleteUserModal.style.display = 'none';
                });
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === createUserModal) {
                    createUserModal.style.display = 'none';
                }
                if (event.target === editUserModal) {
                    editUserModal.style.display = 'none';
                }
                if (event.target === resetPasswordModal) {
                    resetPasswordModal.style.display = 'none';
                }
                if (event.target === deleteUserModal) {
                    deleteUserModal.style.display = 'none';
                }
            });
            
            // Password confirmation validation
            document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
                const newPassword = document.getElementById('reset_new_password').value;
                const confirmPassword = document.getElementById('reset_confirm_password').value;
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                }
            });
        });
    </script>
</body>
</html>