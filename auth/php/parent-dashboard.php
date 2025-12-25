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
    <title>Parent Dashboard - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/portal.css">
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
                    <a href="parent-profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
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
                        <a href="#">Academics <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="../../pages/timetable.html">Class Timetable</a></li>
                            <li><a href="../../pages/courses.html">Courses</a></li>
                            <li><a href="#">Progress Reports</a></li>
                            <li><a href="#">Attendance</a></li>
                        </ul>
                    </li>
                    <li><a href="../../pages/notice.html">Notice Board</a></li>
                    <li><a href="../../pages/calendar.html">Calendar</a></li>
                    <li><a href="../../pages/gallery.html">Gallery</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="portal-header">
        <div class="container">
            <div class="welcome-section">
                
                <div class="welcome-text">
                    <h1><i class="fas fa-user-friends"></i> Parent Dashboard</h1>
                    <p>Welcome back, <span id="parentName"><?php echo htmlspecialchars($parent['full_name']); ?></span>!</p>
                </div>
            </div>
        </div>
    </section>

    <section class="portal-content">
        <div class="container">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Children</h3>
                    <p><?php echo count($children); ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-tasks"></i>
                    <h3>Pending Fees</h3>
                    <p>0</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Avg. Attendance</h3>
                    <p>92%</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Upcoming Events</h3>
                    <p>3</p>
                </div>
            </div>

            <div class="portal-grid">
                <div class="portal-widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-calendar-alt"></i> Children's Schedule</h3>
                    </div>
                    <div class="widget-content">
                        <ul class="schedule-list">
                            <li>
                                <span class="time">09:00 - 10:00</span>
                                <span class="subject">Mathematics - John</span>
                                <span class="room">Room 101</span>
                            </li>
                            <li>
                                <span class="time">10:15 - 11:15</span>
                                <span class="subject">Science - Jane</span>
                                <span class="room">Lab 2</span>
                            </li>
                            <li>
                                <span class="time">11:30 - 12:30</span>
                                <span class="subject">English - Both</span>
                                <span class="room">Room 205</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="portal-widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-file-invoice-dollar"></i> Fee Status</h3>
                    </div>
                    <div class="widget-content">
                        <ul class="assignments-list">
                            <li>
                                <span class="assignment-title">Monthly Tuition - John</span>
                                <span class="due-date">Due: Dec 20</span>
                                <span class="priority paid">Paid</span>
                            </li>
                            <li>
                                <span class="assignment-title">Exam Fees - Jane</span>
                                <span class="due-date">Due: Dec 22</span>
                                <span class="priority paid">Paid</span>
                            </li>
                            <li>
                                <span class="assignment-title">Annual Charges</span>
                                <span class="due-date">Due: Dec 25</span>
                                <span class="priority paid">Paid</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="portal-widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-bell"></i> Recent Notices</h3>
                    </div>
                    <div class="widget-content">
                        <ul class="notices-list">
                            <li>
                                <span class="notice-title">Annual Sports Day - Dec 28</span>
                                <span class="notice-date">Posted 2 days ago</span>
                            </li>
                            <li>
                                <span class="notice-title">Parent-Teacher Meeting</span>
                                <span class="notice-date">Posted 1 week ago</span>
                            </li>
                            <li>
                                <span class="notice-title">Holiday Schedule</span>
                                <span class="notice-date">Posted 2 weeks ago</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="portal-widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-chart-bar"></i> Performance Overview</h3>
                    </div>
                    <div class="widget-content">
                        <div class="performance-chart">
                            <canvas id="performanceChart" height="200"></canvas>
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
    <script src="../js/parent-portal.js"></script>
</body>
</html>

