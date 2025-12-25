<?php
session_start();
include_once '../../config/dbconnection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'parent') {
    header('Location: parent-login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch parent data from both users and user_profiles tables
try {
    $stmt = $pdo->prepare("SELECT u.*, up.phone, up.date_of_birth, up.gender, up.address, up.occupation, up.relationship FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = ?");
    $stmt->execute([$user_id]);
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If columns don't exist yet, fall back to basic query
    if (strpos($e->getMessage(), 'Unknown column') !== false) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Add default empty values for missing profile fields
        $parent['phone'] = '';
        $parent['date_of_birth'] = '';
        $parent['gender'] = '';
        $parent['address'] = '';
        $parent['occupation'] = '';
        $parent['relationship'] = '';
        
        // Show a message indicating the database needs to be updated
        echo "<div class='alert alert-warning' style='margin: 20px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; color: #856404;'>";
        echo "<strong>Database Update Required:</strong> Please run the database update script to enable full profile functionality. ";
        echo "<a href='../../run_manual_update.php' style='color: #007bff; text-decoration: underline;'>Click here to update</a>";
        echo "</div>";
    } else {
        // Re-throw the exception if it's a different error
        throw $e;
    }
}

// Split full_name into first and last name for display purposes
$name_parts = explode(' ', $parent['full_name'], 2);
$parent['first_name'] = $name_parts[0];
$parent['last_name'] = isset($name_parts[1]) ? $name_parts[1] : '';


// Fetch children data
try {
    // First try to fetch using parent_id column
    $stmt = $pdo->prepare("SELECT * FROM students WHERE parent_id = ?");
    $stmt->execute([$user_id]);
    $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If parent_id column doesn't exist, fetch all students (temporary workaround)
    if (strpos($e->getMessage(), 'Unknown column') !== false) {
        $stmt = $pdo->prepare("SELECT * FROM students");
        $stmt->execute();
        $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Show a message indicating the database needs to be updated
        echo "<div class='alert alert-warning' style='margin: 20px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; color: #856404;'>";
        echo "<strong>Database Update Required:</strong> Please run the database update script to properly link parents with their children. ";
        echo "<a href='../../run_manual_update.php' style='color: #007bff; text-decoration: underline;'>Click here to update</a>";
        echo "</div>";
    } else {
        // Re-throw the exception if it's a different error
        throw $e;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Profile - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
                    <a href="parent-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
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
            <h1><i class="fas fa-user-circle"></i> Parent Profile</h1>
        </div>
    </section>

    <section class="profile-content">
        <div class="container">
            <div class="profile-grid">
                <div class="profile-sidebar">
                    <div class="profile-card">
                        <div class="avatar-container">
                            <img src="<?php echo !empty($parent['avatar']) ? '../../uploads/avatars/' . htmlspecialchars($parent['avatar']) : '../../images/default-avatar.png'; ?>" alt="Profile Picture" class="avatar" id="profileAvatar">
                            <button class="change-avatar-btn" id="changeAvatarBtn">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <h3 id="parentName"><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></h3>
                        <p id="parentRole">Parent</p>
                        <div class="profile-stats">
                            <div class="stat">
                                <span class="stat-value"><?php echo count($children); ?></span>
                                <span class="stat-label">Children</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">0</span>
                                <span class="stat-label">Pending Fees</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-nav">
                        <ul>
                            <li class="active"><a href="#overview"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
                            <li><a href="#personal-info"><i class="fas fa-user"></i> Personal Info</a></li>
                            <li><a href="#academic-details"><i class="fas fa-graduation-cap"></i> Academic Details</a></li>
                            <li><a href="#settings"><i class="fas fa-cog"></i> Settings</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="profile-main">
                    <!-- Overview Section -->
                    <div class="profile-section active" id="overview">
                        <h2><i class="fas fa-chart-line"></i> Dashboard Overview</h2>
                        <div class="dashboard-widgets">
                            <div class="widget">
                                <div class="widget-header">
                                    <h3><i class="fas fa-calendar-alt"></i> Upcoming Events</h3>
                                </div>
                                <div class="widget-content">
                                    <ul class="events-list">
                                        <li>
                                            <span class="event-date">Dec 20</span>
                                            <span class="event-title">Final Exams</span>
                                        </li>
                                        <li>
                                            <span class="event-date">Dec 25</span>
                                            <span class="event-title">Christmas Holiday</span>
                                        </li>
                                        <li>
                                            <span class="event-date">Jan 01</span>
                                            <span class="event-title">New Year Celebration</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="widget">
                                <div class="widget-header">
                                    <h3><i class="fas fa-bell"></i> Recent Notices</h3>
                                </div>
                                <div class="widget-content">
                                    <ul class="notices-list">
                                        <li>
                                            <span class="notice-title">Annual Sports Day</span>
                                            <span class="notice-date">Posted 2 days ago</span>
                                        </li>
                                        <li>
                                            <span class="notice-title">Library Timing Changes</span>
                                            <span class="notice-date">Posted 1 week ago</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Personal Information Section -->
                    <div class="profile-section" id="personal-info">
                        <h2><i class="fas fa-user-edit"></i> Personal Information</h2>
                        
                        <!-- Avatar Upload Form -->
                        <div class="avatar-upload-section">
                            <h3><i class="fas fa-image"></i> Profile Picture</h3>
                            <div class="avatar-preview">
                                <img src="<?php echo !empty($parent['avatar']) ? '../../uploads/avatars/' . htmlspecialchars($parent['avatar']) : '../../images/default-avatar.png'; ?>" alt="Profile Picture" class="avatar-preview-img">
                                <button class="btn btn-primary" onclick="document.getElementById('avatarUploadModal').style.display='block'">
                                    <i class="fas fa-camera"></i> Change Avatar
                                </button>
                            </div>
                        </div>
                        
                        <form id="personalInfoForm" method="POST" action="update-parent-profile.php">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($parent['first_name']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($parent['last_name']); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($parent['email']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($parent['phone']); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dateOfBirth">Date of Birth</label>
                                    <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($parent['date_of_birth']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select id="gender" name="gender">
                                        <option value="male" <?php echo ($parent['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ($parent['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo ($parent['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($parent['address']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="occupation">Occupation</label>
                                <input type="text" id="occupation" name="occupation" value="<?php echo htmlspecialchars($parent['occupation']); ?>">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="childrenCount">Number of Children</label>
                                    <input type="number" id="childrenCount" name="childrenCount" value="<?php echo count($children); ?>" min="1" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="relationship">Relationship</label>
                                    <select id="relationship" name="relationship">
                                        <option value="father" <?php echo ($parent['relationship'] === 'father') ? 'selected' : ''; ?>>Father</option>
                                        <option value="mother" <?php echo ($parent['relationship'] === 'mother') ? 'selected' : ''; ?>>Mother</option>
                                        <option value="guardian" <?php echo ($parent['relationship'] === 'guardian') ? 'selected' : ''; ?>>Guardian</option>
                                    </select>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </form>
                    </div>
                    
                    <!-- Academic Details Section -->
                    <div class="profile-section" id="academic-details">
                        <h2><i class="fas fa-graduation-cap"></i> Children's Academic Details</h2>
                        
                        <div class="children-accordion">
                            <?php foreach ($children as $index => $child): ?>
                            <div class="child-section">
                                <div class="child-header">
                                    <h3><i class="fas fa-user-graduate"></i> <?php echo htmlspecialchars($child['full_name']); ?> (Grade <?php echo htmlspecialchars($child['grade_level']); ?>)</h3>
                                    <button class="expand-btn"><i class="fas fa-chevron-down"></i></button>
                                </div>
                                <div class="child-content">
                                    <div class="academic-summary">
                                        <div class="summary-card">
                                            <h4>Current Average</h4>
                                            <p class="grade">85%</p>
                                        </div>
                                        <div class="summary-card">
                                            <h4>Attendance</h4>
                                            <p class="attendance">92%</p>
                                        </div>
                                        <div class="summary-card">
                                            <h4>Rank</h4>
                                            <p class="rank">5th / 42</p>
                                        </div>
                                    </div>
                                    
                                    <div class="subject-performance">
                                        <h4>Subject-wise Performance</h4>
                                        <table class="performance-table">
                                            <thead>
                                                <tr>
                                                    <th>Subject</th>
                                                    <th>Teacher</th>
                                                    <th>Current Grade</th>
                                                    <th>Last Exam</th>
                                                    <th>Progress</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Mathematics</td>
                                                    <td>Mr. Sharma</td>
                                                    <td class="grade-a">A</td>
                                                    <td>88%</td>
                                                    <td><div class="progress-bar"><div class="progress-fill" style="width: 88%; background: #4CAF50;"></div></div></td>
                                                </tr>
                                                <tr>
                                                    <td>Science</td>
                                                    <td>Dr. KC</td>
                                                    <td class="grade-a">A</td>
                                                    <td>90%</td>
                                                    <td><div class="progress-bar"><div class="progress-fill" style="width: 90%; background: #4CAF50;"></div></div></td>
                                                </tr>
                                                <tr>
                                                    <td>English</td>
                                                    <td>Mrs. Thapa</td>
                                                    <td class="grade-b">B+</td>
                                                    <td>82%</td>
                                                    <td><div class="progress-bar"><div class="progress-fill" style="width: 82%; background: #2196F3;"></div></div></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="action-buttons">
                                        <button class="btn btn-outline" onclick="downloadReport('<?php echo $child['id']; ?>')">
                                            <i class="fas fa-download"></i> Download Report
                                        </button>
                                        <button class="btn btn-outline" onclick="contactTeacher('<?php echo $child['id']; ?>')">
                                            <i class="fas fa-comments"></i> Contact Teacher
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Settings Section -->
                    <div class="profile-section" id="settings">
                        <h2><i class="fas fa-cog"></i> Account Settings</h2>
                        
                        <div class="settings-section">
                            <h3><i class="fas fa-bell"></i> Notification Preferences</h3>
                            <div class="setting-options">
                                <div class="setting-option">
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider"></span>
                                    </label>
                                    <span>Email Notifications</span>
                                </div>
                                
                                <div class="setting-option">
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider"></span>
                                    </label>
                                    <span>SMS Alerts</span>
                                </div>
                                
                                <div class="setting-option">
                                    <label class="switch">
                                        <input type="checkbox">
                                        <span class="slider"></span>
                                    </label>
                                    <span>Push Notifications</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h3><i class="fas fa-lock"></i> Security Settings</h3>
                            <div class="setting-options">
                                <div class="setting-option">
                                    <button class="btn btn-outline" onclick="changePassword()">
                                        <i class="fas fa-key"></i> Change Password
                                    </button>
                                </div>
                                
                                <div class="setting-option">
                                    <button class="btn btn-outline" onclick="enable2FA()">
                                        <i class="fas fa-shield-alt"></i> Two-Factor Authentication
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h3><i class="fas fa-user-circle"></i> Profile Visibility</h3>
                            <div class="setting-options">
                                <div class="setting-option">
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider"></span>
                                    </label>
                                    <span>Allow teachers to view my contact information</span>
                                </div>
                                
                                <div class="setting-option">
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider"></span>
                                    </label>
                                    <span>Show my profile to other parents</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h3><i class="fas fa-trash-alt"></i> Account Management</h3>
                            <div class="setting-options">
                                <div class="setting-option">
                                    <button class="btn btn-danger" onclick="downloadData()">
                                        <i class="fas fa-download"></i> Download My Data
                                    </button>
                                </div>
                                
                                <div class="setting-option">
                                    <button class="btn btn-danger" onclick="deactivateAccount()">
                                        <i class="fas fa-user-slash"></i> Deactivate Account
                                    </button>
                                </div>
                            </div>
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

    <script src="../js/parent-profile.js"></script>
    <script>
        function downloadReport(childId) {
            alert('Downloading report for child ID: ' + childId);
            // In a real implementation, this would trigger a report download
        }
        
        function contactTeacher(childId) {
            alert('Contacting teacher for child ID: ' + childId);
            // In a real implementation, this would open a messaging interface
        }
        
        function changePassword() {
            alert('Change password functionality would open here');
            // In a real implementation, this would open a password change form
        }
        
        function enable2FA() {
            alert('Two-factor authentication setup would open here');
            // In a real implementation, this would open 2FA setup
        }
        
        function downloadData() {
            alert('Preparing data download...');
            // In a real implementation, this would prepare user data for download
        }
        
        function deactivateAccount() {
            if (confirm('Are you sure you want to deactivate your account? This action cannot be undone.')) {
                alert('Account deactivation initiated');
                // In a real implementation, this would send a request to deactivate the account
            }
        }
    </script>

</body>
</html>
