<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user info from session
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];
$userRole = $_SESSION['user_role'];

// Database connection
require_once '../../config/dbconnection.php';

// Handle avatar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/avatars/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['avatar']['tmp_name']);
        
        if (in_array($fileType, $allowedTypes)) {
            // Generate unique filename
            $fileExtension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $newFilename = uniqid() . '_' . $_SESSION['user_id'] . '.' . $fileExtension;
            $uploadPath = $uploadDir . $newFilename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
                try {
                    // Check if user profile exists
                    $stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($profile) {
                        // Update existing profile
                        $stmt = $pdo->prepare("UPDATE user_profiles SET avatar = ? WHERE user_id = ?");
                        $stmt->execute([$newFilename, $_SESSION['user_id']]);
                    } else {
                        // Create new profile
                        $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, avatar) VALUES (?, ?)");
                        $stmt->execute([$_SESSION['user_id'], $newFilename]);
                    }
                    
                    $avatarSuccess = "Avatar uploaded successfully!";
                } catch (PDOException $e) {
                    $avatarError = "Database error: " . $e->getMessage();
                }
            } else {
                $avatarError = "Failed to move uploaded file.";
            }
        } else {
            $avatarError = "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
        }
    } else {
        $avatarError = "Please select a file to upload.";
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $dateOfBirth = $_POST['dateOfBirth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    
    // Set default values for notification preferences
    $emailNotifications = true;
    $smsAlerts = false;
    $pushNotifications = false;
    
    try {
        // Update users table
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        $stmt->execute([$fullName, $email, $_SESSION['user_id']]);
        
        // Check if user profile exists
        $stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($profile) {
            // Update existing profile
            $stmt = $pdo->prepare("UPDATE user_profiles SET phone = ?, date_of_birth = ?, gender = ?, address = ?, email_notifications = ?, sms_alerts = ?, push_notifications = ? WHERE user_id = ?");
            $stmt->execute([$phone, $dateOfBirth, $gender, $address, $emailNotifications, $smsAlerts, $pushNotifications, $_SESSION['user_id']]);
        } else {
            // Create new profile
            $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, phone, date_of_birth, gender, address, email_notifications, sms_alerts, push_notifications) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $phone, $dateOfBirth, $gender, $address, $emailNotifications, $smsAlerts, $pushNotifications]);
        }
        
        $profileSuccess = "Profile updated successfully!";
        
        // Update session variables
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_email'] = $email;
        
        // Refresh user data
        $userName = $fullName;
        $userEmail = $email;
    } catch (PDOException $e) {
        $profileError = "Database error: " . $e->getMessage();
    }
}

// Handle notification preferences update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_notifications'])) {
    $emailNotifications = isset($_POST['emailNotifications']) ? 1 : 0;
    $smsAlerts = isset($_POST['smsAlerts']) ? 1 : 0;
    $pushNotifications = isset($_POST['pushNotifications']) ? 1 : 0;
    
    try {
        // Check if user profile exists
        $stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($profile) {
            // Update existing profile
            $stmt = $pdo->prepare("UPDATE user_profiles SET email_notifications = ?, sms_alerts = ?, push_notifications = ? WHERE user_id = ?");
            $stmt->execute([$emailNotifications, $smsAlerts, $pushNotifications, $_SESSION['user_id']]);
        } else {
            // Create new profile with notification preferences
            $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, email_notifications, sms_alerts, push_notifications) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $emailNotifications, $smsAlerts, $pushNotifications]);
        }
        
        $notificationsSuccess = "Notification preferences updated successfully!";
        
        // Refresh notification preferences
        $emailNotifications = $_POST['emailNotifications'] ?? true;
        $smsAlerts = $_POST['smsAlerts'] ?? false;
        $pushNotifications = $_POST['pushNotifications'] ?? false;
    } catch (PDOException $e) {
        $notificationsError = "Database error: " . $e->getMessage();
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmNewPassword = $_POST['confirmNewPassword'] ?? '';
    
    // Validate input
    if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
        $passwordError = "Please fill in all password fields.";
    } elseif ($newPassword !== $confirmNewPassword) {
        $passwordError = "New passwords do not match.";
    } elseif (strlen($newPassword) < 6) {
        $passwordError = "New password must be at least 6 characters long.";
    } else {
        try {
            // Get current password hash from database
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($currentPassword, $user['password_hash'])) {
                // Hash new password
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Update password in database
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$newPasswordHash, $_SESSION['user_id']]);
                
                $passwordSuccess = "Password updated successfully!";
            } else {
                $passwordError = "Current password is incorrect.";
            }
        } catch (PDOException $e) {
            $passwordError = "Database error: " . $e->getMessage();
        }
    }
}

// Get user details from database
try {
    // Get user data from users table
    $stmt = $pdo->prepare("SELECT id, email, full_name, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $userName = $user['full_name'];
        $userEmail = $user['email'];
        $userRole = $user['role'];
    }
    
    // Get profile data from user_profiles table
    $stmt = $pdo->prepare("SELECT phone, date_of_birth, gender, address, avatar, email_notifications, sms_alerts, push_notifications FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $profileData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($profileData) {
        $phone = $profileData['phone'] ?? '';
        $dateOfBirth = $profileData['date_of_birth'] ?? '';
        $gender = $profileData['gender'] ?? '';
        $address = $profileData['address'] ?? '';
        $avatar = $profileData['avatar'] ?? '';
        $emailNotifications = $profileData['email_notifications'] ?? true;
        $smsAlerts = $profileData['sms_alerts'] ?? false;
        $pushNotifications = $profileData['push_notifications'] ?? false;
    } else {
        $phone = '';
        $dateOfBirth = '';
        $gender = '';
        $address = '';
        $avatar = '';
        $emailNotifications = true;
        $smsAlerts = false;
        $pushNotifications = false;
    }
    
    // Get academic details for students
    if ($userRole === 'student') {
        // Get student information
        $stmt = $pdo->prepare("SELECT s.student_id, s.grade_level, s.enrollment_date, c.class_name, c.section FROM students s LEFT JOIN classes c ON s.id = c.id WHERE s.user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($studentData) {
            $studentId = $studentData['student_id'] ?? '';
            $gradeLevel = $studentData['grade_level'] ?? '';
            $enrollmentDate = $studentData['enrollment_date'] ?? '';
            $className = $studentData['class_name'] ?? '';
            $section = $studentData['section'] ?? '';
        } else {
            $studentId = '';
            $gradeLevel = '';
            $enrollmentDate = '';
            $className = '';
            $section = '';
        }
        
        // Get course schedule
        $stmt = $pdo->prepare("SELECT s.subject_name, t.full_name as teacher_name, cs.schedule, c.room_number FROM class_subjects cs JOIN subjects s ON cs.subject_id = s.id JOIN teachers t ON cs.teacher_id = t.id JOIN classes c ON cs.class_id = c.id WHERE c.class_name = ? AND c.section = ?");
        $stmt->execute([$className, $section]);
        $courseSchedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get academic performance (mock data for now)
        $gpa = '3.85';
        $rank = '5th out of 42';
        $attendanceRate = '95%';
    }
} catch (PDOException $e) {
    // Handle database error
    $error = "Database error: " . $e->getMessage();
    $phone = '';
    $dateOfBirth = '';
    $gender = '';
    $address = '';
    $avatar = '';
    $emailNotifications = true;
    $smsAlerts = false;
    $pushNotifications = false;
    $studentId = '';
    $gradeLevel = '';
    $enrollmentDate = '';
    $className = '';
    $section = '';
    $courseSchedule = [];
    $gpa = '';
    $rank = '';
    $attendanceRate = '';
}?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../../js/push-notifications.js"></script>
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            transform: translateZ(0);
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .alert:hover {
            transform: translateY(-2px) translateZ(0);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            border-radius: 15px;
            width: 50%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            transform: translateZ(0);
            animation: slideIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .close-modal {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #aaa;
        }
        .close-modal:hover {
            color: #000;
            transform: scale(1.1);
        }
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 3fr;
            gap: 30px;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #4CAF50;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        @media (max-width: 768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* 3D Section Transitions */
        @keyframes sectionEntrance {
            0% {
                opacity: 0;
                transform: translateZ(-100px) rotateX(30deg) rotateY(15deg);
            }
            100% {
                opacity: 1;
                transform: translateZ(0) rotateX(0deg) rotateY(0deg);
            }
        }
        
        @keyframes sectionExit {
            0% {
                opacity: 1;
                transform: translateZ(0) rotateX(0deg) rotateY(0deg);
            }
            100% {
                opacity: 0;
                transform: translateZ(-100px) rotateX(-30deg) rotateY(-15deg);
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
                    <?php if ($userRole === 'admin'): ?>
                        <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
                    <?php elseif ($userRole === 'student'): ?>
                        <a href="student-portal.php"><i class="fas fa-graduation-cap"></i> Student Portal</a>
                    <?php endif; ?>
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
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="navbar" id="navbar">
            <div class="container">
                <ul class="nav-menu" id="navMenu">
                    <li><a href="../../index.php">Home</a></li>
                    <li class="dropdown">
                        <a href="#">About <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="../../pages/history.html">History</a></li>
                            <li><a href="../../pages/vision-mission.html">Vision & Mission</a></li>
                            <li><a href="../../pages/principal-message.html">Principal's Message</a></li>
                            <li><a href="../../pages/faculty.html">Faculty</a></li>
                            <li><a href="../../pages/management.html">Management</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#">Academics <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="../../pages/courses.html">Courses Offered</a></li>
                            <li><a href="../../pages/computer-science.html">+2 Computer Science</a></li>
                            <li><a href="../../pages/hotel-management.html">+2 Hotel Management</a></li>
                            <li><a href="../../pages/timetable.html">Class Timetable</a></li>
                            <li><a href="../../pages/calendar.html">Academic Calendar</a></li>
                        </ul>
                    </li>
                    <li><a href="../../pages/admission.html">Admissions</a></li>
                    <li><a href="../../pages/notice.html">Notice Board</a></li>
                    <li><a href="../../pages/gallery.html">Gallery</a></li>
                    <li><a href="../../pages/contact.html">Contact</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="profile-header">
        <div class="container">
            <h1><i class="fas fa-user-circle"></i> User Profile</h1>
        </div>
    </section>

    <section class="profile-content">
        <div class="container">
            <div class="profile-grid">
                <div class="profile-sidebar">
                    <div class="profile-card" style="transform: translateZ(0); transition: all 0.3s ease; position: relative;">
                        <div class="avatar-container" style="position: relative; display: inline-block; perspective: 1000px;">
                            <?php if (!empty($avatar)): ?>
                                <img src="../../uploads/avatars/<?php echo htmlspecialchars($avatar); ?>" alt="Profile Picture" class="avatar" id="profileAvatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #4CAF50; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); transform: translateZ(0); cursor: pointer;" onmouseover="this.style.transform='scale(1.05) translateZ(0)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.2)';" onmouseout="this.style.transform='scale(1) translateZ(0)'; this.style.boxShadow='0 5px 15px rgba(0, 0, 0, 0.1)';" onclick="document.getElementById('avatarUploadModal').style.display='block';">
                            <?php else: ?>
                                <img src="../../images/default-avatar.png" alt="Profile Picture" class="avatar" id="profileAvatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #4CAF50; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); transform: translateZ(0); cursor: pointer;" onmouseover="this.style.transform='scale(1.05) translateZ(0)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.2)';" onmouseout="this.style.transform='scale(1) translateZ(0)'; this.style.boxShadow='0 5px 15px rgba(0, 0, 0, 0.1)';" onclick="document.getElementById('avatarUploadModal').style.display='block';">
                            <?php endif; ?>
                            <button class="change-avatar-btn" id="changeAvatarBtn" onclick="document.getElementById('avatarUploadModal').style.display='block'" style="position: absolute; bottom: 0; right: 0; background: #4CAF50; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid white; transition: all 0.3s ease; box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2); transform: translateZ(0);">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <h3 id="userName" style="margin: 20px 0 10px; color: #333; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;"><?php echo htmlspecialchars($userName); ?></h3>
                        <p id="userRole" style="margin: 0 0 20px; color: #666; font-style: italic; transition: all 0.3s ease;"><?php echo ucfirst(htmlspecialchars($userRole)); ?></p>
                        <div class="profile-stats" style="display: flex; justify-content: space-around; margin-top: 20px;">
                            <div class="stat" style="text-align: center; padding: 15px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; transform: translateZ(0);">
                                <span class="stat-value" style="display: block; font-size: 24px; font-weight: bold; color: #4CAF50;">12</span>
                                <span class="stat-label" style="display: block; font-size: 14px; color: #666;">Courses</span>
                            </div>
                            <div class="stat" style="text-align: center; padding: 15px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; transform: translateZ(0);">
                                <span class="stat-value" style="display: block; font-size: 24px; font-weight: bold; color: #4CAF50;">85%</span>
                                <span class="stat-label" style="display: block; font-size: 14px; color: #666;">Attendance</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-nav" style="margin-top: 30px;">
                        <ul style="list-style: none; padding: 0;">
                            <li class="active" style="margin-bottom: 10px; border-radius: 8px; overflow: hidden; transition: all 0.3s ease; transform: translateZ(0); box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);">
                                <a href="#overview" style="display: block; padding: 15px; color: #4CAF50; font-weight: 600; text-decoration: none; background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border-left: 4px solid #4CAF50; transition: all 0.3s ease;">
                                    <i class="fas fa-tachometer-alt" style="margin-right: 10px;"></i> Overview
                                </a>
                            </li>
                            <li style="margin-bottom: 10px; border-radius: 8px; overflow: hidden; transition: all 0.3s ease; transform: translateZ(0); box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);">
                                <a href="#personal-info" style="display: block; padding: 15px; color: #555; font-weight: 600; text-decoration: none; background: white; border-left: 4px solid transparent; transition: all 0.3s ease;">
                                    <i class="fas fa-user" style="margin-right: 10px;"></i> Personal Info
                                </a>
                            </li>
                            <li style="margin-bottom: 10px; border-radius: 8px; overflow: hidden; transition: all 0.3s ease; transform: translateZ(0); box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);">
                                <a href="#academic-details" style="display: block; padding: 15px; color: #555; font-weight: 600; text-decoration: none; background: white; border-left: 4px solid transparent; transition: all 0.3s ease;">
                                    <i class="fas fa-graduation-cap" style="margin-right: 10px;"></i> Academic Details
                                </a>
                            </li>
                            <li style="border-radius: 8px; overflow: hidden; transition: all 0.3s ease; transform: translateZ(0); box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);">
                                <a href="#settings" style="display: block; padding: 15px; color: #555; font-weight: 600; text-decoration: none; background: white; border-left: 4px solid transparent; transition: all 0.3s ease;">
                                    <i class="fas fa-cog" style="margin-right: 10px;"></i> Settings
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="profile-main">
                    <div class="profile-section active" id="overview" style="transform: translateZ(0); transition: all 0.3s ease; position: relative;">
                        <h2 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eee;"><i class="fas fa-chart-line"></i> Dashboard Overview</h2>
                        <div class="dashboard-widgets" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                            <div class="widget" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); transform: translateZ(0); transition: all 0.3s ease; position: relative;">
                                <div class="widget-header" style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                                    <h3 style="color: #4CAF50; margin: 0;"><i class="fas fa-calendar-alt"></i> Upcoming Events</h3>
                                </div>
                                <div class="widget-content">
                                    <ul class="events-list" style="list-style: none; padding: 0;">
                                        <li style="padding: 15px 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s ease;">
                                            <span class="event-date" style="background: #e8f5e9; color: #4CAF50; padding: 5px 10px; border-radius: 20px; font-size: 14px; font-weight: bold;">Dec 20</span>
                                            <span class="event-title" style="font-weight: 600; color: #333;">Final Exams</span>
                                        </li>
                                        <li style="padding: 15px 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s ease;">
                                            <span class="event-date" style="background: #e8f5e9; color: #4CAF50; padding: 5px 10px; border-radius: 20px; font-size: 14px; font-weight: bold;">Dec 25</span>
                                            <span class="event-title" style="font-weight: 600; color: #333;">Christmas Holiday</span>
                                        </li>
                                        <li style="padding: 15px 0; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s ease;">
                                            <span class="event-date" style="background: #e8f5e9; color: #4CAF50; padding: 5px 10px; border-radius: 20px; font-size: 14px; font-weight: bold;">Jan 01</span>
                                            <span class="event-title" style="font-weight: 600; color: #333;">New Year Celebration</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="widget" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); transform: translateZ(0); transition: all 0.3s ease; position: relative;">
                                <div class="widget-header" style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                                    <h3 style="color: #4CAF50; margin: 0;"><i class="fas fa-bell"></i> Recent Notices</h3>
                                </div>
                                <div class="widget-content">
                                    <ul class="notices-list" style="list-style: none; padding: 0;">
                                        <li style="padding: 15px 0; border-bottom: 1px solid #f0f0f0; transition: all 0.3s ease;">
                                            <span class="notice-title" style="display: block; font-weight: 600; color: #333; margin-bottom: 5px;">Annual Sports Day</span>
                                            <span class="notice-date" style="font-size: 14px; color: #666;">Posted 2 days ago</span>
                                        </li>
                                        <li style="padding: 15px 0; transition: all 0.3s ease;">
                                            <span class="notice-title" style="display: block; font-weight: 600; color: #333; margin-bottom: 5px;">Library Timing Changes</span>
                                            <span class="notice-date" style="font-size: 14px; color: #666;">Posted 1 week ago</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-section" id="personal-info" style="transform: translateZ(0) rotateX(15deg); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; display: none; perspective: 1000px; transform-style: preserve-3d; opacity: 0;">
                        <h2 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eee; transform: translateZ(0); transition: all 0.3s ease;"><i class="fas fa-user-edit"></i> Personal Information</h2>
                        <?php if (isset($profileError)): ?>
                            <div class="alert alert-error" style="transform: translateZ(0); transition: all 0.3s ease;">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($profileError); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($profileSuccess)): ?>
                            <div class="alert alert-success" style="transform: translateZ(0); transition: all 0.3s ease;">
                                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($profileSuccess); ?>
                            </div>
                        <?php endif; ?>
                        <form method="post" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 15px; padding: 30px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.7); transform: translateZ(0) rotateX(0deg); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; perspective: 1000px; transform-style: preserve-3d; overflow: hidden; margin-bottom: 30px;">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="form-row" style="display: flex; gap: 20px; margin-bottom: 20px; perspective: 1000px; transform-style: preserve-3d;">
                                <div class="form-group" style="margin-bottom: 20px; position: relative; transform: translateZ(0); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); perspective: 1000px; transform-style: preserve-3d; flex: 1;">
                                    <label for="fullName" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555; transition: all 0.3s ease;">Full Name</label>
                                    <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($userName); ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-sizing: border-box; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); transform-style: preserve-3d; transform: translateZ(0); position: relative;">
                                </div>
                                <div class="form-group" style="margin-bottom: 20px; position: relative; transform: translateZ(0); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); perspective: 1000px; transform-style: preserve-3d; flex: 1;">
                                    <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555; transition: all 0.3s ease;">Email Address</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-sizing: border-box; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); transform-style: preserve-3d; transform: translateZ(0); position: relative;">
                                </div>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 20px; position: relative; transform: translateZ(0); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); perspective: 1000px; transform-style: preserve-3d;">
                                <label for="phone" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555; transition: all 0.3s ease;">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-sizing: border-box; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); transform-style: preserve-3d; transform: translateZ(0); position: relative;">
                            </div>
                            
                            <div class="form-row" style="display: flex; gap: 20px; margin-bottom: 20px; perspective: 1000px; transform-style: preserve-3d;">
                                <div class="form-group" style="margin-bottom: 20px; position: relative; transform: translateZ(0); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); perspective: 1000px; transform-style: preserve-3d; flex: 1;">
                                    <label for="dateOfBirth" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555; transition: all 0.3s ease;">Date of Birth</label>
                                    <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($dateOfBirth); ?>" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-sizing: border-box; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); transform-style: preserve-3d; transform: translateZ(0); position: relative;">
                                </div>
                                <div class="form-group" style="margin-bottom: 20px; position: relative; transform: translateZ(0); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); perspective: 1000px; transform-style: preserve-3d; flex: 1;">
                                    <label for="gender" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555; transition: all 0.3s ease;">Gender</label>
                                    <select id="gender" name="gender" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-sizing: border-box; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); transform-style: preserve-3d; transform: translateZ(0); position: relative;">
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php echo ($gender === 'male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ($gender === 'female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo ($gender === 'other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 20px; position: relative; transform: translateZ(0); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); perspective: 1000px; transform-style: preserve-3d;">
                                <label for="address" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555; transition: all 0.3s ease;">Address</label>
                                <textarea id="address" name="address" rows="3" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-sizing: border-box; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); resize: vertical; min-height: 100px; transform-style: preserve-3d; transform: translateZ(0); position: relative;"><?php echo htmlspecialchars($address); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; text-align: center; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2); transform: translateZ(0) rotateX(0deg); position: relative; cursor: pointer; transform-style: preserve-3d; overflow: hidden; display: inline-block;">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </form>
                    </div>
                    
                    <!-- Academic Details Section -->
                    <div class="profile-section" id="academic-details" style="transform: translateZ(0) rotateX(15deg); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; display: none; perspective: 1000px; transform-style: preserve-3d; opacity: 0;">
                        <h2 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eee; transform: translateZ(0); transition: all 0.3s ease;"><i class="fas fa-graduation-cap"></i> Academic Details</h2>
                        
                        <?php if ($userRole === 'student'): ?>
                        <div style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 15px; padding: 30px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.7); transform: translateZ(0) rotateX(0deg); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; margin-bottom: 30px; perspective: 1000px; transform-style: preserve-3d; overflow: hidden;">
                            <h3 style="color: #4CAF50; margin-top: 0; transform: translateZ(0); transition: all 0.3s ease;"><i class="fas fa-book"></i> Current Enrollment</h3>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; perspective: 1000px; transform-style: preserve-3d;">
                                <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 20px; border-radius: 10px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25); transform: translateZ(0) rotateX(0deg); transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; perspective: 1000px; transform-style: preserve-3d; overflow: hidden;" class="summary-card">
                                    <h4 style="margin: 0 0 10px 0; color: #333;">Class Information</h4>
                                    <p style="margin: 5px 0; color: #666;"><strong>Grade:</strong> <?php echo !empty($gradeLevel) ? htmlspecialchars($gradeLevel) : 'Not enrolled'; ?></p>
                                    <p style="margin: 5px 0; color: #666;"><strong>Section:</strong> <?php echo !empty($section) ? htmlspecialchars($section) : 'N/A'; ?></p>
                                    <p style="margin: 5px 0; color: #666;"><strong>Academic Year:</strong> <?php echo date('Y') . '-' . (date('Y') + 1); ?></p>
                                </div>
                                
                                <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 20px; border-radius: 10px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25); transform: translateZ(0) rotateX(0deg); transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; perspective: 1000px; transform-style: preserve-3d; overflow: hidden;" class="summary-card">
                                    <h4 style="margin: 0 0 10px 0; color: #333;">Performance</h4>
                                    <p style="margin: 5px 0; color: #666;"><strong>GPA:</strong> <?php echo htmlspecialchars($gpa); ?></p>
                                    <p style="margin: 5px 0; color: #666;"><strong>Rank:</strong> <?php echo htmlspecialchars($rank); ?></p>
                                    <p style="margin: 5px 0; color: #666;"><strong>Attendance:</strong> <?php echo htmlspecialchars($attendanceRate); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 15px; padding: 30px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.7); transform: translateZ(0) rotateX(0deg); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; perspective: 1000px; transform-style: preserve-3d; overflow: hidden;">
                            <h3 style="color: #4CAF50; margin-top: 0; transform: translateZ(0); transition: all 0.3s ease;"><i class="fas fa-list"></i> Course Schedule</h3>
                            
                            <div style="overflow-x: auto; transform: translateZ(0); transition: all 0.3s ease;">
                                <table style="width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); border-radius: 8px; overflow: hidden; transform: translateZ(0); transition: all 0.2s ease;">
                                    <thead>
                                        <tr style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; position: relative; transform-style: preserve-3d;">
                                            <th style="padding: 12px; text-align: left; transform: translateZ(0); transition: all 0.2s ease;">Subject</th>
                                            <th style="padding: 12px; text-align: left; transform: translateZ(0); transition: all 0.2s ease;">Teacher</th>
                                            <th style="padding: 12px; text-align: left; transform: translateZ(0); transition: all 0.2s ease;">Time</th>
                                            <th style="padding: 12px; text-align: left; transform: translateZ(0); transition: all 0.2s ease;">Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($courseSchedule)): ?>
                                            <?php foreach ($courseSchedule as $index => $course): ?>
                                                <tr style="border-bottom: 1px solid #eee; <?php echo $index % 2 == 1 ? 'background: #f9f9f9;' : ''; ?> transition: all 0.2s ease; transform-style: preserve-3d;" class="course-row">
                                                    <td style="padding: 12px; transform: translateZ(0); transition: all 0.2s ease;"><?php echo htmlspecialchars($course['subject_name']); ?></td>
                                                    <td style="padding: 12px; transform: translateZ(0); transition: all 0.2s ease;"><?php echo htmlspecialchars($course['teacher_name']); ?></td>
                                                    <td style="padding: 12px; transform: translateZ(0); transition: all 0.2s ease;"><?php echo htmlspecialchars($course['schedule'] ?? 'TBD'); ?></td>
                                                    <td style="padding: 12px; transform: translateZ(0); transition: all 0.2s ease;"><?php echo htmlspecialchars($course['room_number'] ?? 'TBD'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" style="padding: 12px; text-align: center; color: #666;">No courses scheduled</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php else: ?>
                        <div style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 15px; padding: 30px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.7); transform: translateZ(0) rotateX(0deg); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; text-align: center; perspective: 1000px; transform-style: preserve-3d; overflow: hidden;">
                            <h3 style="color: #4CAF50; margin-top: 0; transform: translateZ(0); transition: all 0.3s ease;"><i class="fas fa-info-circle"></i> Academic Information</h3>
                            <p>Academic details are only available for student accounts.</p>
                        </div>
                        <?php endif; ?>
                    </div>                    
                    <!-- Settings Section -->
                    <div class="profile-section" id="settings" style="transform: translateZ(0) rotateX(15deg); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; display: none; perspective: 1000px; transform-style: preserve-3d; opacity: 0;">
                        <h2 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eee; transform: translateZ(0); transition: all 0.3s ease;"><i class="fas fa-cog"></i> Account Settings</h2>
                        
                        <div style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 15px; padding: 30px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.7); transform: translateZ(0) rotateX(0deg); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; margin-bottom: 30px; perspective: 1000px; transform-style: preserve-3d; overflow: hidden;">
                            <h3 style="color: #4CAF50; margin-top: 0; transform: translateZ(0); transition: all 0.3s ease;"><i class="fas fa-key"></i> Change Password</h3>
                            
                            <?php if (isset($passwordError)): ?>
                                <div class="alert alert-error" style="transform: translateZ(0); transition: all 0.3s ease;">
                                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($passwordError); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($passwordSuccess)): ?>
                                <div class="alert alert-success" style="transform: translateZ(0); transition: all 0.3s ease;">
                                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($passwordSuccess); ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="post" style="margin-top: 20px;">
                                <input type="hidden" name="change_password" value="1">
                                <div class="form-group" style="margin-bottom: 20px; position: relative; transform: translateZ(0); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); perspective: 1000px; transform-style: preserve-3d;">
                                    <label for="currentPassword" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555; transition: all 0.3s ease;">Current Password</label>
                                    <input type="password" id="currentPassword" name="currentPassword" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-sizing: border-box; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); transform-style: preserve-3d; transform: translateZ(0); position: relative;">
                                </div>
                                
                                <div class="form-row" style="display: flex; gap: 20px; margin-bottom: 20px; perspective: 1000px; transform-style: preserve-3d;">
                                    <div class="form-group" style="margin-bottom: 20px; position: relative; transform: translateZ(0); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); perspective: 1000px; transform-style: preserve-3d; flex: 1;">
                                        <label for="newPassword" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555; transition: all 0.3s ease;">New Password</label>
                                        <input type="password" id="newPassword" name="newPassword" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-sizing: border-box; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); transform-style: preserve-3d; transform: translateZ(0); position: relative;">
                                    </div>
                                    <div class="form-group" style="margin-bottom: 20px; position: relative; transform: translateZ(0); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); perspective: 1000px; transform-style: preserve-3d; flex: 1;">
                                        <label for="confirmNewPassword" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555; transition: all 0.3s ease;">Confirm New Password</label>
                                        <input type="password" id="confirmNewPassword" name="confirmNewPassword" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-sizing: border-box; box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1); transform-style: preserve-3d; transform: translateZ(0); position: relative;">
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; text-align: center; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2); transform: translateZ(0) rotateX(0deg); position: relative; cursor: pointer; transform-style: preserve-3d; overflow: hidden; display: inline-block;">
                                    <i class="fas fa-sync-alt"></i> Update Password
                                </button>
                            </form>
                        </div>
                        
                        <?php if (isset($notificationsError)): ?>
                            <div class="alert alert-error" style="transform: translateZ(0); transition: all 0.3s ease;">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($notificationsError); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($notificationsSuccess)): ?>
                            <div class="alert alert-success" style="transform: translateZ(0); transition: all 0.3s ease;">
                                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($notificationsSuccess); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 15px; padding: 30px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.7); transform: translateZ(0) rotateX(0deg); transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); position: relative; perspective: 1000px; transform-style: preserve-3d; overflow: hidden;">
                            <h3 style="color: #4CAF50; margin-top: 0; transform: translateZ(0); transition: all 0.3s ease;"><i class="fas fa-bell"></i> Notification Preferences</h3>
                            
                            <form method="post">
                                <input type="hidden" name="update_notifications" value="1">
                                <div style="margin-top: 20px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #eee; transition: all 0.3s ease; transform-style: preserve-3d;" class="setting-option">
                                        <div>
                                            <h4 style="margin: 0 0 5px 0; color: #333;">Email Notifications</h4>
                                            <p style="margin: 0; color: #666; font-size: 14px;">Receive updates via email</p>
                                        </div>
                                        <label class="switch" style="transform-style: preserve-3d; perspective: 1000px;">
                                            <input type="checkbox" name="emailNotifications" <?php echo $emailNotifications ? 'checked' : ''; ?>>
                                            <span class="slider" style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.3), 0 3px 5px rgba(0,0,0,0.2); transform: translateZ(0);"></span>
                                        </label>
                                    </div>
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #eee; transition: all 0.3s ease; transform-style: preserve-3d;" class="setting-option">
                                        <div>
                                            <h4 style="margin: 0 0 5px 0; color: #333;">SMS Alerts</h4>
                                            <p style="margin: 0; color: #666; font-size: 14px;">Receive text messages for important updates</p>
                                        </div>
                                        <label class="switch" style="transform-style: preserve-3d; perspective: 1000px;">
                                            <input type="checkbox" name="smsAlerts" <?php echo $smsAlerts ? 'checked' : ''; ?>>
                                            <span class="slider" style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.3), 0 3px 5px rgba(0,0,0,0.2); transform: translateZ(0);"></span>
                                        </label>
                                    </div>
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; transition: all 0.3s ease; transform-style: preserve-3d;" class="setting-option">
                                        <div>
                                            <h4 style="margin: 0 0 5px 0; color: #333;">Push Notifications</h4>
                                            <p style="margin: 0; color: #666; font-size: 14px;">Receive mobile app notifications</p>
                                        </div>
                                        <label class="switch" style="transform-style: preserve-3d; perspective: 1000px;">
                                            <input type="checkbox" name="pushNotifications" <?php echo $pushNotifications ? 'checked' : ''; ?>>
                                            <span class="slider" style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.3), 0 3px 5px rgba(0,0,0,0.2); transform: translateZ(0);"></span>
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; text-align: center; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2); transform: translateZ(0) rotateX(0deg); position: relative; cursor: pointer; transform-style: preserve-3d; overflow: hidden; display: inline-block; margin-top: 20px;">
                                    <i class="fas fa-save"></i> Save Preferences
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
        // Handle profile navigation tabs with 3D transitions
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.profile-nav a');
            const sections = document.querySelectorAll('.profile-section');
            
            // Initialize first section as active
            if (sections.length > 0) {
                sections[0].classList.add('active');
                sections[0].style.display = 'block';
                sections[0].style.animation = 'sectionEntrance 0.7s cubic-bezier(0.25, 0.8, 0.25, 1) forwards';
            }
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all tabs
                    tabs.forEach(t => t.parentElement.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.parentElement.classList.add('active');
                    
                    // Get target section
                    const targetId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);
                    
                    // Hide all sections with 3D exit animation
                    sections.forEach(section => {
                        if (section.classList.contains('active')) {
                            section.style.animation = 'sectionExit 0.5s cubic-bezier(0.25, 0.8, 0.25, 1) forwards';
                            setTimeout(() => {
                                section.style.display = 'none';
                                section.classList.remove('active');
                            }, 500);
                        }
                    });
                    
                    // Show target section with 3D entrance animation
                    setTimeout(() => {
                        targetSection.style.display = 'block';
                        targetSection.classList.add('active');
                        targetSection.style.animation = 'sectionEntrance 0.7s cubic-bezier(0.25, 0.8, 0.25, 1) forwards';
                    }, 250);
                });
            });
            
            // Handle modal close when clicking outside
            const modal = document.getElementById('avatarUploadModal');
            if (modal) {
                window.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            }
            
            // Add 3D hover effects to profile nav items
            const navItems = document.querySelectorAll('.profile-nav li');
            navItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('active')) {
                        this.style.transform = 'translateY(-3px) translateZ(10px) rotateX(5deg)';
                        this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
                    }
                });
                
                item.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.transform = 'translateY(0) translateZ(0) rotateX(0deg)';
                        this.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.05)';
                    }
                });
            });
            
            // Make toggle switches functional
            const sliders = document.querySelectorAll('.slider');
            sliders.forEach(slider => {
                slider.addEventListener('click', function() {
                    const checkbox = this.previousElementSibling;
                    
                    // Toggle the checkbox state
                    checkbox.checked = !checkbox.checked;
                    
                    // Update slider appearance based on checkbox state
                    if (checkbox.checked) {
                        this.style.backgroundColor = '#4CAF50';
                        this.style.boxShadow = 'inset 0 1px 3px rgba(0,0,0,0.3), 0 3px 10px rgba(76, 175, 80, 0.4)';
                    } else {
                        this.style.backgroundColor = '#ccc';
                        this.style.boxShadow = 'inset 0 1px 3px rgba(0,0,0,0.3), 0 3px 5px rgba(0,0,0,0.2)';
                    }
                    
                    // Special handling for push notifications
                    if (checkbox.name === 'pushNotifications') {
                        handlePushNotificationToggle(checkbox.checked);
                    }
                });
            });
            
            // Handle push notification toggle
            function handlePushNotificationToggle(isEnabled) {
                if (isEnabled) {
                    // Request permission and subscribe
                    if (typeof PushNotifications !== 'undefined') {
                        PushNotifications.requestPermission()
                            .then(permissionGranted => {
                                if (permissionGranted) {
                                    return PushNotifications.subscribe();
                                }
                                return false;
                            })
                            .then(subscribed => {
                                if (!subscribed) {
                                    // Revert the toggle if subscription failed
                                    const pushCheckbox = document.querySelector('input[name="pushNotifications"]');
                                    if (pushCheckbox) {
                                        pushCheckbox.checked = false;
                                        const slider = pushCheckbox.nextElementSibling;
                                        if (slider) {
                                            slider.style.backgroundColor = '#ccc';
                                            slider.style.boxShadow = 'inset 0 1px 3px rgba(0,0,0,0.3), 0 3px 5px rgba(0,0,0,0.2)';
                                        }
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error enabling push notifications:', error);
                                // Revert the toggle if there was an error
                                const pushCheckbox = document.querySelector('input[name="pushNotifications"]');
                                if (pushCheckbox) {
                                    pushCheckbox.checked = false;
                                    const slider = pushCheckbox.nextElementSibling;
                                    if (slider) {
                                        slider.style.backgroundColor = '#ccc';
                                        slider.style.boxShadow = 'inset 0 1px 3px rgba(0,0,0,0.3), 0 3px 5px rgba(0,0,0,0.2)';
                                    }
                                }
                            });
                    } else {
                        console.log('Push notifications library not loaded');
                        // Revert the toggle if library is not available
                        const pushCheckbox = document.querySelector('input[name="pushNotifications"]');
                        if (pushCheckbox) {
                            pushCheckbox.checked = false;
                            const slider = pushCheckbox.nextElementSibling;
                            if (slider) {
                                slider.style.backgroundColor = '#ccc';
                                slider.style.boxShadow = 'inset 0 1px 3px rgba(0,0,0,0.3), 0 3px 5px rgba(0,0,0,0.2)';
                            }
                        }
                    }
                } else {
                    // Unsubscribe from push notifications
                    if (typeof PushNotifications !== 'undefined') {
                        PushNotifications.unsubscribe()
                            .catch(error => {
                                console.error('Error disabling push notifications:', error);
                            });
                    }
                }
            }
            
            // Add 3D hover effects to form elements
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach(group => {
                group.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) translateZ(15px) rotateX(5deg)';
                    this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.1)';
                });
                
                group.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) translateZ(0) rotateX(0deg)';
                    this.style.boxShadow = 'none';
                });
            });
            
            // Add 3D hover effects to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) translateZ(15px) rotateX(8deg)';
                    this.style.boxShadow = '0 12px 30px rgba(0, 0, 0, 0.3)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) translateZ(0) rotateX(0deg)';
                    this.style.boxShadow = '0 6px 15px rgba(0, 0, 0, 0.2)';
                });
                
                button.addEventListener('mousedown', function() {
                    this.style.transform = 'translateY(2px) translateZ(5px) rotateX(2deg)';
                    this.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.25)';
                });
            });
            
            // Add 3D hover effects to table rows
            const tableRows = document.querySelectorAll('.course-row');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateZ(5px)';
                    this.style.boxShadow = '0 3px 10px rgba(0, 0, 0, 0.1)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateZ(0)';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
</body>
</html>

<!-- Avatar Upload Modal -->
<div id="avatarUploadModal" class="modal">
    <div class="modal-content" style="position: relative;">
        <span class="close-modal" onclick="document.getElementById('avatarUploadModal').style.display='none'">&times;</span>
        <h2><i class="fas fa-cloud-upload-alt"></i> Upload New Avatar</h2>
        <p style="margin-bottom: 20px; color: #666;">Choose an image file (JPG, PNG, or GIF) to update your profile picture.</p>
        
        <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #4CAF50;">
            <strong>Requirements:</strong>
            <ul style="margin: 10px 0 0 20px; color: #2e7d32;">
                <li>File formats: JPG, PNG, or GIF</li>
                <li>Maximum file size: 5MB</li>
                <li>Recommended dimensions: 200x200 pixels</li>
            </ul>
        </div>
        
        <?php if (isset($avatarError)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($avatarError); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($avatarSuccess)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($avatarSuccess); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="avatar">Choose Image:</label>
                <input type="file" id="avatar" name="avatar" accept="image/*" required style="padding: 15px; border: 2px dashed #4CAF50; border-radius: 8px; background-color: #f9f9f9; width: 100%; box-sizing: border-box; transition: all 0.3s ease;">
                <p style="margin-top: 10px; font-size: 14px; color: #666;">Or drag and drop an image file here</p>
            </div>
            <div class="form-group" style="display: flex; gap: 10px; margin-top: 20px;">
                <input type="submit" name="upload_avatar" value="Upload Avatar" class="btn btn-primary" style="flex: 1; background: #4CAF50; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); transform: translateZ(0); position: relative; cursor: pointer;">
                <button type="button" onclick="document.getElementById('avatarUploadModal').style.display='none'" class="btn btn-secondary" style="flex: 1; background: #6c757d; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; text-align: center; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); transform: translateZ(0); position: relative; cursor: pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

