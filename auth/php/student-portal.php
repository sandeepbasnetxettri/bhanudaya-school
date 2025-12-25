<?php
session_start();

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
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
    <title>Student Portal - Excellence School</title>
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
                    <a href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
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
                </div>
            </div>
        </div>
    </header>

    <section class="portal-header">
        <div class="container">
            <h1><i class="fas fa-graduation-cap"></i> Student Portal</h1>
            <p>Welcome back, <span id="studentName"><?php echo htmlspecialchars($userName); ?></span>!</p>
        </div>
    </section>

    <section class="portal-content">
        <div class="container">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-book"></i>
                    <h3>Courses Enrolled</h3>
                    <p>6</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-tasks"></i>
                    <h3>Pending Assignments</h3>
                    <p>3</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Average Grade</h3>
                    <p>85%</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Attendance Rate</h3>
                    <p>92%</p>
                </div>
            </div>

            <div class="portal-grid">
                <div class="portal-widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-calendar-alt"></i> Today's Schedule</h3>
                    </div>
                    <div class="widget-content">
                        <ul class="schedule-list">
                            <li>
                                <span class="time">09:00 - 10:00</span>
                                <span class="subject">Mathematics</span>
                                <span class="room">Room 101</span>
                            </li>
                            <li>
                                <span class="time">10:15 - 11:15</span>
                                <span class="subject">Science</span>
                                <span class="room">Lab 2</span>
                            </li>
                            <li>
                                <span class="time">11:30 - 12:30</span>
                                <span class="subject">English</span>
                                <span class="room">Room 205</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="portal-widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-tasks"></i> Pending Assignments</h3>
                    </div>
                    <div class="widget-content">
                        <ul class="assignments-list">
                            <li>
                                <span class="assignment-title">Math Homework #5</span>
                                <span class="due-date">Due: Dec 20</span>
                                <span class="priority high">High</span>
                            </li>
                            <li>
                                <span class="assignment-title">Science Project</span>
                                <span class="due-date">Due: Dec 22</span>
                                <span class="priority medium">Medium</span>
                            </li>
                            <li>
                                <span class="assignment-title">English Essay</span>
                                <span class="due-date">Due: Dec 25</span>
                                <span class="priority low">Low</span>
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
                                <span class="notice-title">Library Timing Changes</span>
                                <span class="notice-date">Posted 1 week ago</span>
                            </li>
                            <li>
                                <span class="notice-title">Exam Schedule Published</span>
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

    <script src="../js/student-portal.js"></script>
    <script>
        // Enhanced student portal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                // Add delay for staggered animation
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animated-card');
                
                // Add hover effect enhancement
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Add animation to portal widgets
            const widgets = document.querySelectorAll('.portal-widget');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            widgets.forEach(widget => {
                widget.style.opacity = '0';
                widget.style.transform = 'translateY(20px)';
                widget.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(widget);
            });
            
            // Add click effects to list items
            const listItems = document.querySelectorAll('.schedule-list li, .assignments-list li, .notices-list li');
            listItems.forEach(item => {
                item.addEventListener('click', function() {
                    this.style.backgroundColor = '#f0f8ff';
                    setTimeout(() => {
                        this.style.backgroundColor = '';
                    }, 300);
                });
            });
            
            // Add performance chart initialization
            const performanceChart = document.getElementById('performanceChart');
            if (performanceChart) {
                // This would typically be replaced with actual charting library initialization
                performanceChart.style.backgroundColor = '#f8f9fa';
                performanceChart.style.borderRadius = '8px';
                performanceChart.style.display = 'flex';
                performanceChart.style.alignItems = 'center';
                performanceChart.style.justifyContent = 'center';
                performanceChart.style.color = '#666';
                performanceChart.textContent = 'Performance Chart Visualization';
                performanceChart.style.fontWeight = 'bold';
            }
        });
    </script>
</body>
</html>
