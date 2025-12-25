<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Timetable - Excellence School</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
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
            <h1>Class Timetable</h1>
            <p><a href="../index.html">Home</a> / <a href="#">Academics</a> / Timetable</p>
        </div>
    </div>

    <section class="page-content">
        <div class="container">
            <div class="timetable-controls">
                <h2>Class Schedule 2025</h2>
                <div class="controls-wrapper">
                    <div class="class-selector">
                        <label for="classSelect"><i class="fas fa-graduation-cap"></i> Select Class:</label>
                        <select id="classSelect">
                            <option value="1">Class 1</option>
                            <option value="2">Class 2</option>
                            <option value="3">Class 3</option>
                            <option value="4">Class 4</option>
                            <option value="5">Class 5</option>
                            <option value="6">Class 6</option>
                            <option value="7">Class 7</option>
                            <option value="8">Class 8</option>
                            <option value="9">Class 9</option>
                            <option value="10" selected>Class 10</option>
                            <option value="11-cs">+2 Computer Science</option>
                            <option value="11-hm">+2 Hotel Management</option>
                        </select>
                    </div>
                    <button class="btn btn-secondary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Timetable
                    </button>
                    <button class="btn btn-primary" id="downloadBtn">
                        <i class="fas fa-download"></i> Download PDF
                    </button>
                </div>
            </div>

            <div class="timetable-info">
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <div>
                        <strong>School Hours</strong>
                        <p>9:00 AM - 4:00 PM</p>
                    </div>
                </div>
                <div class="info-card">
                    <i class="fas fa-calendar-day"></i>
                    <div>
                        <strong>Working Days</strong>
                        <p>Sunday - Friday</p>
                    </div>
                </div>
                <div class="info-card">
                    <i class="fas fa-coffee"></i>
                    <div>
                        <strong>Break Time</strong>
                    </div>
                </div>
                <div class="info-card">
                    <i class="fas fa-book"></i>
                    <div>
                        <strong>Periods per Day</strong>
                        <p>7 Periods</p>
                    </div>
                </div>
            </div>

            <div class="timetable-container" id="timetableContainer">
                <!-- Timetable will be dynamically loaded here -->
            </div>

            <div class="timetable-legend">
                <h3>Subject Codes & Teachers</h3>
                <div class="legend-grid" id="legendGrid">
                    <!-- Legend will be dynamically loaded -->
                </div>
            </div>

            <div class="download-section">
                <h3>Download Timetables</h3>
                <div class="download-grid">
                    <a href="#" class="download-card" download>
                        <i class="fas fa-file-pdf"></i>
                        <h4>Primary (Class 1-5)</h4>
                        <span>Download PDF</span>
                    </a>
                    <a href="#" class="download-card" download>
                        <i class="fas fa-file-pdf"></i>
                        <h4>Lower Secondary (6-8)</h4>
                        <span>Download PDF</span>
                    </a>
                    <a href="#" class="download-card" download>
                        <i class="fas fa-file-pdf"></i>
                        <h4>Secondary (9-10)</h4>
                        <span>Download PDF</span>
                    </a>
                    <a href="#" class="download-card" download>
                        <i class="fas fa-file-pdf"></i>
                        <h4>+2 All Programs</h4>
                        <span>Download PDF</span>
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
                <p>&copy; 2025 Excellence School. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../js/main.js"></script>
    <script src="../js/timetable.js"></script>
</body>
</html>
