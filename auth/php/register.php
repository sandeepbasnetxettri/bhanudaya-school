<?php
session_start();
// Database connection
require_once '../../config/dbconnection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = $_POST['role'];
    $parentName = isset($_POST['parentName']) ? $_POST['parentName'] : '';
    
    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
        $error = "Please fill in all fields.";
    } elseif ($role === 'admin') {
        $error = "Invalid role selection. Please select a valid role.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($role === 'student' && empty($parentName)) {
        $error = "Please enter your parent/guardian's name.";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingUser) {
                $error = "Email already registered. Please use a different email.";
            } else {
                // Hash password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user into database
                $fullName = $firstName . ' ' . $lastName;
                $stmt = $pdo->prepare("INSERT INTO users (email, full_name, password_hash, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$email, $fullName, $passwordHash, $role]);
                
                // Get the inserted user ID
                $userId = $pdo->lastInsertId();
                
                // Ensure user_profiles entry exists for all users
                $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id) VALUES (?)");
                $stmt->execute([$userId]);
                
                // Update phone number in user_profiles if provided
                if (!empty($phone)) {
                    $stmt = $pdo->prepare("UPDATE user_profiles SET phone = ? WHERE user_id = ?");
                    $stmt->execute([$phone, $userId]);
                }
                
                // Handle role-specific data
                switch ($role) {
                    case 'student':
                        // Generate a unique student ID
                        $studentId = 'STU' . str_pad($userId, 6, '0', STR_PAD_LEFT);
                        
                        // Find parent ID by name
                        $parentId = null;
                        if (!empty($parentName)) {
                            $stmt = $pdo->prepare("SELECT u.id FROM users u WHERE u.full_name = ? AND u.role = 'parent'");
                            $stmt->execute([$parentName]);
                            $parent = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($parent) {
                                $parentId = $parent['id'];
                            }
                        }
                        
                        // Insert into students table with parent_id
                        $stmt = $pdo->prepare("INSERT INTO students (user_id, student_id, full_name, phone, email, parent_guardian_name, parent_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$userId, $studentId, $fullName, $phone, $email, $parentName, $parentId]);
                        break;
                    case 'teacher':
                        // Generate a unique employee ID
                        $employeeId = 'EMP' . str_pad($userId, 6, '0', STR_PAD_LEFT);
                        // Insert into teachers table
                        $stmt = $pdo->prepare("INSERT INTO teachers (user_id, employee_id, full_name, phone, email) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$userId, $employeeId, $fullName, $phone, $email]);
                        break;
                    case 'parent':
                        // Parent information is stored in user_profiles and can be updated later
                        break;
                }
                
                // Set session variables
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $fullName;
                $_SESSION['user_role'] = $role;
                
                // Redirect based on role
                switch ($role) {
                    case 'admin':
                        header('Location: admin-dashboard.php');
                        break;
                    case 'student':
                        header('Location: student-portal.php');
                        break;
                    case 'teacher':
                        header('Location: teacher-dashboard.php');
                        break;
                    case 'parent':
                        header('Location: parent-portal.php');
                        break;
                    default:
                        header('Location: ../../index.php');
                }
                exit;
            }
        } catch (PDOException $e) {
            $error = "Registration failed. Please try again. (" . $e->getMessage() . ")";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h2><i class="fas fa-user-plus"></i> Register</h2>
                <p>Create a new account to access our services.</p>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form id="registerForm" action="" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-user"></i></span>
                            <input type="text" id="firstName" name="firstName" required placeholder="Enter your first name" value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-user"></i></span>
                            <input type="text" id="lastName" name="lastName" required placeholder="Enter your last name" value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number (Optional)</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-phone"></i></span>
                        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" name="password" required placeholder="Create a password">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirm your password">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-user-tag"></i></span>
                        <select id="role" name="role" required>
                            <option value="">Select your role</option>
                            <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
                            <option value="Admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'Admin') ? 'selected' : 'Admin'; ?>>Admin</option>
                            <option value="teacher" <?php echo (isset($_POST['role']) && $_POST['role'] === 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                            <option value="parent" <?php echo (isset($_POST['role']) && $_POST['role'] === 'parent') ? 'selected' : ''; ?>>Parent</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" id="parentNameGroup" style="display: none;">
                    <label for="parentName">Parent/Guardian Name</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-user-friends"></i></span>
                        <input type="text" id="parentName" name="parentName" placeholder="Enter your parent/guardian's full name" value="<?php echo isset($_POST['parentName']) ? htmlspecialchars($_POST['parentName']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-container">
                        <input type="checkbox" name="terms" required>
                        I agree to the <a href="#" target="_blank">Terms and Conditions</a>
                        <span class="checkmark"></span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>  
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login now</a></p>
                <p><a href="../../index.php"><i class="fas fa-home"></i> Back to Home</a></p>
            </div>
        </div>
    </div>
    <script src="../js/signup.js"></script>
    <script>
        // Show/hide parent name field based on role selection
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const parentNameGroup = document.getElementById('parentNameGroup');
            
            roleSelect.addEventListener('change', function() {
                if (this.value === 'student') {
                    parentNameGroup.style.display = 'block';
                } else {
                    parentNameGroup.style.display = 'none';
                }
            });
            
            // Trigger change event on page load to handle pre-selected values
            if (roleSelect.value === 'student') {
                parentNameGroup.style.display = 'block';
            }
        });
    </script>
</body>
</html>
