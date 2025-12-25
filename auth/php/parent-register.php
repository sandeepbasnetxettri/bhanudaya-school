<?php
session_start();
include_once '../../config/dbconnection.php';

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'parent') {
        header('Location: parent-dashboard.php');
        exit();
    } else {
        header('Location: ../' . $_SESSION['user_role'] . '-dashboard.php');
        exit();
    }
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['firstName'];
    $last_name = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $relationship = $_POST['relationship'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];
    
    // Validate input
    if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($phone) && 
        !empty($relationship) && !empty($password) && !empty($confirm_password)) {
        
        // Check if passwords match
        if ($password !== $confirm_password) {
            $error_message = 'Passwords do not match';
        } else {
            // Check if parent ID or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$parent_id, $email]);
            $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing_user) {
                $error_message = 'Parent ID or Email already exists';
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Combine first and last name
                $full_name = $first_name . ' ' . $last_name;
                
                // Insert new parent user
                $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, full_name, role) VALUES (?, ?, ?, 'parent')");
                
                try {
                    $stmt->execute([$email, $hashed_password, $full_name]);
                    $user_id = $pdo->lastInsertId();
                    
                    // Insert phone number and relationship into user_profiles table
                    $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, phone, relationship) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $phone, $relationship]);
                    $success_message = 'Registration successful! You can now login.';
                } catch (PDOException $e) {
                    $error_message = 'Registration failed. Please try again.';
                }
            }
        }
    } else {
        $error_message = 'Please fill in all fields';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Registration - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <div class="logo-section">
                    <img src="../../images/school-logo.png" alt="School Logo" class="auth-logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%234CAF50%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2240%22 fill=%22white%22%3ES%3C/text%3E%3C/svg%3E'">
                    <h2>Bhanudaya Secondary School</h2>
                </div>
                <h1>Parent Registration</h1>
                <p>Create an account to access your parent portal</p>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <form id="parentRegisterForm" class="auth-form" method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                    </div>
                </div>
                

                <div class="form-group">
                    <label for="relationship">Relationship</label>
                    <div class="input-with-icon">
                        <i class="fas fa-users"></i>
                        <select id="relationship" name="relationship" required>
                            <option value="">Select Relationship</option>
                            <option value="father" <?php echo (isset($_POST['relationship']) && $_POST['relationship'] === 'father') ? 'selected' : ''; ?>>Father</option>
                            <option value="mother" <?php echo (isset($_POST['relationship']) && $_POST['relationship'] === 'mother') ? 'selected' : ''; ?>>Mother</option>
                            <option value="guardian" <?php echo (isset($_POST['relationship']) && $_POST['relationship'] === 'guardian') ? 'selected' : ''; ?>>Guardian</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Create a password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                    </div>
                </div>
                
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="terms" name="terms" required>
                        <span class="checkmark"></span>
                        I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Register as Parent
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="parent-login.php">Login</a></p>
                <p><a href="../../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
            </div>
        </div>
    </div>

    <script src="../js/signup.js"></script>
</body>
</html>