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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input
    if (!empty($email) && !empty($password)) {
        // Prepare statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT u.*, up.phone FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.email = ? AND u.role = 'parent'");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            // Split full_name into first and last name
            $name_parts = explode(' ', $user['full_name'], 2);
            $_SESSION['first_name'] = $name_parts[0];
            $_SESSION['last_name'] = isset($name_parts[1]) ? $name_parts[1] : '';
            
            // Redirect to parent dashboard
            header('Location: parent-dashboard.php');
            exit();
        } else {
            $error_message = 'Invalid Parent ID or Password';
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
    <title>Parent Login - Excellence School</title>
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
                <h1>Parent Login</h1>
                <p>Access your parent portal to monitor your children's progress</p>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form id="parentLoginForm" class="auth-form" method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="rememberMe" name="rememberMe">
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login to Parent Portal
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Register</a></p>
                <p><a href="../../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
            </div>
        </div>
    </div>

    <script src="../js/login.js"></script>
</body>
</html>