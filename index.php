<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="School Management System - Quality Education for a Brighter Future">
    <title>Excellence School - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navigation.css">
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php 
                        // Handle both session naming conventions
                        $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : (isset($_SESSION['role']) ? $_SESSION['role'] : '');
                        if ($userRole === 'student'): ?>
                            <a href="auth/php/student-portal.php"><i class="fas fa-user-graduate"></i> Student Portal</a>
                        <?php elseif ($userRole === 'parent'): ?>
                            <a href="auth/php/parent-dashboard.php"><i class="fas fa-user-friends"></i> Parent Portal</a>
                        <?php elseif ($userRole === 'admin'): ?>
                            <a href="auth/php/admin-dashboard.php"><i class="fas fa-user-shield"></i> Admin Panel</a>
                        <?php elseif ($userRole === 'teacher'): ?>
                            <a href="auth/php/teacher-dashboard.php"><i class="fas fa-chalkboard-teacher"></i> Teacher Panel</a>
                        <?php endif; ?>
                        <a href="auth/php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="auth/php/login.php"><i class="fas fa-user"></i> Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="main-header">
            <div class="container">
                <div class="header-content">
                    <div class="logo-section">
                        <img src="images/school im.png" alt="School Logo" class="logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%234CAF50%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2240%22 fill=%22white%22%3ES%3C/text%3E%3C/svg%3E'">
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li class="dropdown">
                        <a href="#">About <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="pages/history.php">History</a></li>
                            <li><a href="pages/vision-mission.php">Vision & Mission</a></li>
                            <li><a href="pages/principal-message.php">Principal's Message</a></li>
                            <li><a href="pages/faculty.php">Faculty</a></li>
                            <li><a href="pages/management.php">Management</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#">Academics <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="pages/courses.php">Courses Offered</a></li>
                            <li><a href="pages/computer-science.php">+2 Computer Science</a></li>
                            <li><a href="pages/hotel-management.php">+2 Hotel Management</a></li>
                            <li><a href="pages/timetable.php">Class Timetable</a></li>
                            <li><a href="pages/calendar.php">Academic Calendar</a></li>
                        </ul>
                    </li>
                     <li class="dropdown">
                        <a href="#">Admissions <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="admission.php">Admission Process</a></li>
                            <li><a href="fee-structure.php" class="active">Fee Structure</a></li>
                        </ul>
                    </li>
                    <li><a href="pages/notice.php">Notice Board</a></li>
                    <li><a href="pages/gallery.php">Gallery</a></li>
                    <li><a href="pages/contact.php">Contact</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- School Logo and Student Portal Login Section -->
    
    <section class="banner-slider">
        <div class="slider-container">
            <div class="slide active">
                <img src="images/banner1.jpg" alt="Banner 1" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%221200%22 height=%22500%22%3E%3Crect fill=%22%23667eea%22 width=%221200%22 height=%22500%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2260%22 fill=%22white%22%3EWelcome to Excellence School%3C/text%3E%3C/svg%3E'">
                <div class="slide-content">
                    <h2>Welcome to Excellence School</h2>
                    <p>Nurturing Young Minds for a Bright Future</p>
                    <a href="pages/admission.php" class="btn btn-primary">Apply Now</a>
                </div>
            </div>
            <div class="slide">
                <img src="images/banner2.jpg" alt="Banner 2" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%221200%22 height=%22500%22%3E%3Crect fill=%22%23f093fb%22 width=%221200%22 height=%22500%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2260%22 fill=%22white%22%3EAcademic Excellence%3C/text%3E%3C/svg%3E'">
                <div class="slide-content">
                    <h2>Academic Excellence</h2>
                    <p>Outstanding Results Year After Year</p>
                    <a href="pages/courses.php" class="btn btn-primary">Learn More</a>
                </div>
            </div>
            <div class="slide">
                <img src="images/banner3.jpg" alt="Banner 3" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%221200%22 height=%22500%22%3E%3Crect fill=%22%234facfe%22 width=%221200%22 height=%22500%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2260%22 fill=%22white%22%3EModern Facilities%3C/text%3E%3C/svg%3E'">
                <div class="slide-content">
                    <h2>Modern Facilities</h2>
                    <p>State-of-the-Art Computer & Hotel Management Labs</p>
                    <a href="pages/gallery.php" class="btn btn-primary">View Gallery</a>
                </div>
            </div>
        </div>
        <button class="slider-btn prev" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-btn next" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
        <div class="slider-dots" id="sliderDots"></div>
    </section>

    <section class="quick-links">
        <div class="container">
            <div class="quick-links-grid">
                <a href="pages/admission.php" class="quick-link-card">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Admissions</h3>
                    <p>Apply for admission online</p>
                </a>
                <a href="pages/notice.php" class="quick-link-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Results</h3>
                    <p>Check your exam results</p>
                </a>
                <a href="pages/notice.php" class="quick-link-card">
                    <i class="fas fa-bell"></i>
                    <h3>Notice Board</h3>
                    <p>Latest updates & notices</p>
                </a>
                <a href="pages/contact.php" class="quick-link-card">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Contact Us</h3>
                    <p>Get in touch with us</p>
                </a>
            </div>
        </div>
    </section>

    <section class="news-events">
        <div class="container">
            <h2 class="section-title">Latest News & Events</h2>
            <div class="news-grid" id="newsGrid">
            </div>
            <div class="text-center">
                <a href="pages/notice.php" class="btn btn-secondary">View All</a>
            </div>
        </div>
    </section>

    <section class="programs">
        <div class="container">
            <h2 class="section-title">Our Programs</h2>
            <div class="programs-grid">
                <div class="program-card">
                    <i class="fas fa-school"></i>
                    <h3>Primary Level</h3>
                    <p>Class 1 - 5</p>
                    <a href="pages/courses.php" class="btn btn-outline">Learn More</a>
                </div>
                <div class="program-card">
                    <i class="fas fa-book-reader"></i>
                    <h3>Secondary Level</h3>
                    <p>Class 6 - 10</p>
                    <a href="pages/courses.php" class="btn btn-outline">Learn More</a>
                </div>
                <div class="program-card">
                    <i class="fas fa-laptop-code"></i>
                    <h3>+2 Computer Science</h3>
                    <p>NEB Approved Program</p>
                    <a href="pages/computer-science.php" class="btn btn-outline">Learn More</a>
                </div>
                <div class="program-card">
                    <i class="fas fa-utensils"></i>
                    <h3>+2 Hotel Management</h3>
                    <p>NEB Approved Program</p>
                    <a href="pages/hotel-management.php" class="btn btn-outline">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
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
                <p>&copy; I am kazi.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script src="js/slider.js"></script>
    <script src="js/navigation.js"></script>
</body>
</html>