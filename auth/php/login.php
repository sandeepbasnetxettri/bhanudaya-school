<?php
session_start();

// Database connection
require_once '../../config/dbconnection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            // Prepare statement to prevent SQL injection
            $stmt = $pdo->prepare("SELECT id, email, full_name, password_hash, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Verify password
                if (password_verify($password, $user['password_hash'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Redirect based on role
                    switch ($user['role']) {
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
                            header('Location: parent-dashboard.php');
                            break;
                        default:
                            header('Location: ../../index.php');
                    }
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
                <p>Welcome back! Please login to your account.</p>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form id="loginForm" action="" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="remember-forgot">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember"> Remember me
                            <span class="checkmark"></span>
                        </label>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Register now</a></p>
                <p><a href="../../index.php"><i class="fas fa-home"></i> Back to Home</a></p>
            </div>
        </div>
    </div>

    <script src="../js/login.js"></script>
</body>
</html>