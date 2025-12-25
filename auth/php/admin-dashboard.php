<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
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
    <title>Admin Dashboard - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
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

    <section class="admin-header">
        <div class="container">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($userName); ?>!</p>
        </div>
    </section>

    <section class="admin-content">
        <div class="container">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3>Total Students</h3>
                    <p>1,250</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Total Teachers</h3>
                    <p>42</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-book"></i>
                    <h3>Courses</h3>
                    <p>15</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Events</h3>
                    <p>8</p>
                </div>
            </div>

            <div class="admin-grid">
                <div class="admin-card">
                    <h3><i class="fas fa-user-graduate"></i> Student Management</h3>
                    <p>Manage student records and information</p>
                    <a href="student-management.php" class="btn btn-outline">Manage Students</a>
                </div>

                <div class="admin-card">
                    <h3><i class="fas fa-chalkboard-teacher"></i> Teacher Management</h3>
                    <p>Manage teacher profiles and assignments</p>
                    <a href="teacher-management.php" class="btn btn-outline">Manage Teachers</a>
                </div>

                <div class="admin-card">
                    <h3><i class="fas fa-book-open"></i> Course Management</h3>
                    <p>Manage courses and curriculum</p>
                    <a href="course-management.php" class="btn btn-outline">Manage Courses</a>
                </div>

                <div class="admin-card">
                    <h3><i class="fas fa-bell"></i> Notice Board</h3>
                    <p>Create and manage school notices</p>
                    <a href="notice-management.php" class="btn btn-outline">Manage Notices</a>
                </div>

                <div class="admin-card">
                    <h3><i class="fas fa-calendar-day"></i> Events Management</h3>
                    <p>Schedule and manage school events</p>
                    <a href="event-management.php" class="btn btn-outline">Manage Events</a>
                </div>

                <div class="admin-card">
                    <h3><i class="fas fa-images"></i> Gallery Management</h3>
                    <p>Upload and manage photo gallery</p>
                    <a href="gallery-management.php" class="btn btn-outline">Manage Gallery</a>
                </div>

                <div class="admin-card">
                    <h3><i class="fas fa-clipboard-list"></i> Results Management</h3>
                    <p>Manage student grades and results</p>
                    <a href="results-management.php" class="btn btn-outline">Manage Results</a>
                </div>

                <div class="admin-card">
                    <h3><i class="fas fa-users-cog"></i> User Management</h3>
                    <p>Manage user accounts and permissions</p>
                    <a href="admin-access-editor.php" class="btn btn-outline">Access Editor</a>
                </div>
                
                <?php include 'components/push-notification-form.php'; ?>
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

    <script src="../js/admin.js"></script>
</body>
</html>