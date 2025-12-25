<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses Offered - Excellence School</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body data-page="courses">
    <header class="header">
       <div class="top-bar">
            <div class="container">
                <div class="top-info">
                    <span><i class="fas fa-phone"></i> +977-1-4567890</span>
                    <span><i class="fas fa-envelope"></i> bhanudayahss071@gmail.com</span>
                </div>
                
            </div>
        </div>
        
        <div class="main-header">
            <div class="container">
                <div class="header-content">
                    <div class="logo-section">
                        <img src="../images/school im.png" alt="School Logo" class="logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%234CAF50%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2240%22 fill=%22white%22%3ES%3C/text%3E%3C/svg%3E'">
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
                <ul class="nav-menu" id="navMenu">
                    <li><a href="../index.php">Home</a></li>
                    <li class="dropdown">
                        <a href="#">About <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="history.php">History</a></li>
                            <li><a href="vision-mission.php">Vision & Mission</a></li>
                            <li><a href="principal-message.php">Principal's Message</a></li>
                            <li><a href="faculty.php">Faculty</a></li>
                            <li><a href="management.php">Management</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="active">Academics <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="courses.php">Courses Offered</a></li>
                            <li><a href="computer-science.php">+2 Computer Science</a></li>
                            <li><a href="hotel-management.php">+2 Hotel Management</a></li>
                            <li><a href="timetable.php">Class Timetable</a></li>
                            <li><a href="calendar.php">Academic Calendar</a></li>
                            <li><a href="downloads.php">Downloads</a></li>
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
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <div class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </nav>
    </header>

    <div class="page-banner">
        <div class="container">
            <h1>Courses Offered</h1>
            <p><a href="../index.php">Home</a> / <a href="#">Academics</a> / Courses</p>
        </div>
    </div>

    <section class="page-content">
        <div class="container">
            <div class="courses-intro">
                <h2>Academic Programs at Excellence School</h2>
                <p class="lead">We offer a comprehensive curriculum from Class 1 to +2, designed to nurture young minds and prepare them for future success. Our programs combine academic excellence with holistic development.</p>
            </div>

            <!-- Level Filter -->
            <div class="course-filter">
                <button class="filter-btn active" data-level="all">All Levels</button>
                <button class="filter-btn" data-level="primary">Primary (1-5)</button>
                <button class="filter-btn" data-level="lower-secondary">Lower Secondary (6-8)</button>
                <button class="filter-btn" data-level="secondary">Secondary (9-10)</button>
                <button class="filter-btn" data-level="higher-secondary">+2 Programs</button>
            </div>

            <!-- Primary Level (Class 1-5) -->
            <div class="course-level-section" data-level="primary">
                <div class="level-header">
                    <div class="level-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <div class="level-info">
                        <h2>Primary Level (Class 1-5)</h2>
                        <p>Building strong foundations in core subjects with focus on creativity and exploration</p>
                    </div>
                </div>

                <div class="courses-grid" id="primaryCourses">
                    <!-- Courses will be loaded dynamically -->
                </div>
            </div>

            <!-- Lower Secondary Level (Class 6-8) -->
            <div class="course-level-section" data-level="lower-secondary">
                <div class="level-header">
                    <div class="level-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="level-info">
                        <h2>Lower Secondary Level (Class 6-8)</h2>
                        <p>Expanding knowledge with comprehensive curriculum and skill development</p>
                    </div>
                </div>

                <div class="courses-grid" id="lowerSecondaryCourses">
                    <!-- Courses will be loaded dynamically -->
                </div>
            </div>

            <!-- Secondary Level (Class 9-10) -->
            <div class="course-level-section" data-level="secondary">
                <div class="level-header">
                    <div class="level-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="level-info">
                        <h2>Secondary Level (Class 9-10)</h2>
                        <p>Preparing for SEE examination with comprehensive subject coverage and exam focus</p>
                    </div>
                </div>

                <div class="courses-grid" id="secondaryCourses">
                    <!-- Courses will be loaded dynamically -->
                </div>
            </div>

            <!-- Higher Secondary (+2) -->
            <div class="course-level-section" data-level="higher-secondary">
                <div class="level-header">
                    <div class="level-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="level-info">
                        <h2>Higher Secondary (+2 Programs)</h2>
                        <p>Specialized streams for career-oriented education and higher studies</p>
                    </div>
                </div>

                <div class="programs-grid">
                    <div class="program-card featured">
                        <div class="program-image">
                            <img src="../images/computer-science.jpg" alt="Computer Science" 
                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22250%22%3E%3Crect fill=%22%233498db%22 width=%22400%22 height=%22250%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2230%22 fill=%22white%22%3EComputer Science%3C/text%3E%3C/svg%3E'">
                            <div class="program-badge">Popular</div>
                        </div>
                        <div class="program-content">
                            <h3><i class="fas fa-laptop-code"></i> +2 Computer Science</h3>
                            <p>Comprehensive program in computer science with focus on programming, database management, and modern technologies.</p>
                            
                            <div class="program-highlights">
                                <div class="highlight">
                                    <i class="fas fa-code"></i>
                                    <span>C Programming</span>
                                </div>
                                <div class="highlight">
                                    <i class="fas fa-database"></i>
                                    <span>Database Management</span>
                                </div>
                                <div class="highlight">
                                    <i class="fas fa-flask"></i>
                                    <span>Science Lab</span>
                                </div>
                            </div>

                            <div class="program-stats">
                                <div class="stat">
                                    <strong>Duration:</strong> 2 Years
                                </div>
                                <div class="stat">
                                    <strong>Students:</strong> 120+
                                </div>
                            </div>

                            <a href="computer-science.php" class="btn btn-primary">Learn More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <div class="program-card featured">
                        <div class="program-image">
                            <img src="../images/hotel-management.jpg" alt="Hotel Management" 
                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22250%22%3E%3Crect fill=%22%23e74c3c%22 width=%22400%22 height=%22250%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2230%22 fill=%22white%22%3EHotel Management%3C/text%3E%3C/svg%3E'">
                            <div class="program-badge">High Demand</div>
                        </div>
                        <div class="program-content">
                            <h3><i class="fas fa-hotel"></i> +2 Hotel Management</h3>
                            <p>Professional hospitality program with hands-on training in culinary arts, food service, and hotel operations.</p>
                            
                            <div class="program-highlights">
                                <div class="highlight">
                                    <i class="fas fa-utensils"></i>
                                    <span>Food Production</span>
                                </div>
                                <div class="highlight">
                                    <i class="fas fa-concierge-bell"></i>
                                    <span>F&B Service</span>
                                </div>
                                <div class="highlight">
                                    <i class="fas fa-building"></i>
                                    <span>Front Office</span>
                                </div>
                            </div>

                            <div class="program-stats">
                                <div class="stat">
                                    <strong>Duration:</strong> 2 Years
                                </div>
                                <div class="stat">
                                    <strong>Students:</strong> 80+
                                </div>
                            </div>

                            <a href="hotel-management.php" class="btn btn-primary">Learn More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="course-features">
                <h2>Why Choose Excellence School?</h2>
                <div class="features-grid">
                    <div class="feature-box">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <h4>Expert Faculty</h4>
                        <p>Highly qualified and experienced teachers dedicated to student success</p>
                    </div>
                    <div class="feature-box">
                        <i class="fas fa-book-open"></i>
                        <h4>Modern Curriculum</h4>
                        <p>Updated syllabus aligned with national standards and global practices</p>
                    </div>
                    <div class="feature-box">
                        <i class="fas fa-flask"></i>
                        <h4>Well-Equipped Labs</h4>
                        <p>State-of-the-art science, computer, and practical labs</p>
                    </div>
                    <div class="feature-box">
                        <i class="fas fa-trophy"></i>
                        <h4>Holistic Development</h4>
                        <p>Focus on academics, sports, arts, and character building</p>
                    </div>
                    <div class="feature-box">
                        <i class="fas fa-users"></i>
                        <h4>Small Class Size</h4>
                        <p>Maximum 30 students per class for personalized attention</p>
                    </div>
                    <div class="feature-box">
                        <i class="fas fa-certificate"></i>
                        <h4>100% Pass Rate</h4>
                        <p>Consistent excellent results in board examinations</p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="cta-section">
                <h3>Ready to Join Excellence School?</h3>
                <p>Start your journey towards academic excellence and holistic development</p>
                <div class="cta-buttons">
                    <a href="admission.html" class="btn btn-primary btn-lg">
                        <i class="fas fa-edit"></i> Apply Now
                    </a>
                    <a href="contact.html" class="btn btn-secondary btn-lg">
                        <i class="fas fa-phone"></i> Contact Us
                    </a>
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
                <p>&copy; I am kazi.</p>
            </div>
        </div>
    </footer>

    <script src="../js/main.js"></script>
    <script src="../js/courses.js"></script>
</body>
</html>
