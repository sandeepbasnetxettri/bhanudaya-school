<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Calendar - Excellence School</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body data-page="calendar">
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
            <h1>Academic Calendar 2025</h1>
            <p><a href="../index.php">Home</a> / <a href="#">Academics</a> / Calendar</p>
        </div>
    </div>

    <section class="page-content">
        <div class="container">
            <div class="calendar-header">
                <div class="calendar-info-cards">
                    <div class="info-card">
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <strong>Academic Year</strong>
                            <p>2025 - 2026</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-door-open"></i>
                        <div>
                            <strong>Session Starts</strong>
                            <p>Baisakh 1, 2082</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-door-closed"></i>
                        <div>
                            <strong>Session Ends</strong>
                            <p>Chaitra 30, 2082</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-file-download"></i>
                        <div>
                            <button class="btn btn-sm btn-primary" onclick="downloadCalendar()">
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="calendar-filter">
                <button class="filter-btn active" data-filter="all">All Events</button>
                <button class="filter-btn" data-filter="exam">Examinations</button>
                <button class="filter-btn" data-filter="holiday">Holidays</button>
                <button class="filter-btn" data-filter="event">School Events</button>
                <button class="filter-btn" data-filter="admission">Admissions</button>
            </div>

            <div class="calendar-container" id="calendarContainer">
                <!-- Calendar events will be loaded here -->
            </div>

            <div class="important-dates">
                <h3><i class="fas fa-star"></i> Important Dates at a Glance</h3>
                <div class="dates-grid">
                    <div class="date-card exam-card">
                        <div class="date-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="date-content">
                            <h4>First Terminal Exam</h4>
                            <p>Shrawan 15-25, 2082</p>
                        </div>
                    </div>
                    <div class="date-card exam-card">
                        <div class="date-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="date-content">
                            <h4>Second Terminal Exam</h4>
                            <p>Kartik 20-30, 2082</p>
                        </div>
                    </div>
                    <div class="date-card exam-card">
                        <div class="date-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="date-content">
                            <h4>Final Exam</h4>
                            <p>Falgun 1-15, 2082</p>
                        </div>
                    </div>
                    <div class="date-card event-card">
                        <div class="date-icon"><i class="fas fa-trophy"></i></div>
                        <div class="date-content">
                            <h4>Annual Sports Day</h4>
                            <p>Mangsir 15, 2082</p>
                        </div>
                    </div>
                    <div class="date-card event-card">
                        <div class="date-icon"><i class="fas fa-graduation-cap"></i></div>
                        <div class="date-content">
                            <h4>Annual Day</h4>
                            <p>Falgun 25, 2082</p>
                        </div>
                    </div>
                    <div class="date-card admission-card">
                        <div class="date-icon"><i class="fas fa-user-plus"></i></div>
                        <div class="date-content">
                            <h4>Admission Opens</h4>
                            <p>Chaitra 1, 2082</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="vacation-schedule">
                <h3><i class="fas fa-umbrella-beach"></i> Vacation Schedule</h3>
                <div class="vacation-list">
                    <div class="vacation-item">
                        <div class="vacation-icon"><i class="fas fa-home"></i></div>
                        <div class="vacation-details">
                            <h4>Dashain Vacation</h4>
                            <p><strong>Duration:</strong> Ashwin 10 - Ashwin 25, 2082 (15 days)</p>
                            <p>School reopens on Ashwin 26, 2082</p>
                        </div>
                    </div>
                    <div class="vacation-item">
                        <div class="vacation-icon"><i class="fas fa-diya-lamp"></i></div>
                        <div class="vacation-details">
                            <h4>Tihar Vacation</h4>
                            <p><strong>Duration:</strong> Kartik 26 - Kartik 30, 2082 (5 days)</p>
                            <p>School reopens on Mangsir 1, 2082</p>
                        </div>
                    </div>
                    <div class="vacation-item">
                        <div class="vacation-icon"><i class="fas fa-snowflake"></i></div>
                        <div class="vacation-details">
                            <h4>Winter Vacation</h4>
                            <p><strong>Duration:</strong> Poush 15 - Magh 5, 2082 (20 days)</p>
                            <p>School reopens on Magh 6, 2082</p>
                        </div>
                    </div>
                    <div class="vacation-item">
                        <div class="vacation-icon"><i class="fas fa-sun"></i></div>
                        <div class="vacation-details">
                            <h4>Summer Vacation</h4>
                            <p><strong>Duration:</strong> Chaitra 16 - Chaitra 30, 2082 (15 days)</p>
                            <p>New session begins on Baisakh 1, 2083</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="download-section">
                <h3>Download Calendar</h3>
                <div class="download-grid">
                    <a href="#" class="download-card" download onclick="return downloadCalendar()">
                        <i class="fas fa-file-pdf"></i>
                        <h4>Full Academic Calendar</h4>
                        <span>Download PDF (2025-2026)</span>
                    </a>
                    <a href="#" class="download-card" download>
                        <i class="fas fa-calendar"></i>
                        <h4>Exam Schedule</h4>
                        <span>Download PDF</span>
                    </a>
                    <a href="#" class="download-card" download>
                        <i class="fas fa-list"></i>
                        <h4>Holiday List</h4>
                        <span>Download PDF</span>
                    </a>
                    <a href="#" class="download-card" download>
                        <i class="fas fa-clipboard-list"></i>
                        <h4>Event Schedule</h4>
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
                        <li><i class="fas fa-map-marker-alt"></i> Binayi Triveni Rural Municipality, Dumkibas, Nawalaparasi, Nepal
</li>
                        <li><i class="fas fa-phone"></i> +977-78-620134
</li>
                        <li><i class="fas fa-envelope"></i> bhanudayahss071@gmail.com
</li>
                        <li><i class="fas fa-clock"></i> Sun-Fri: 9:00 AM - 4:00 PM</li>
                    </ul>
                </div>
            </div>
            
        </div>
    </footer>

    <script src="../js/main.js"></script>
    <script src="../js/calendar.js"></script>
</body>
</html>
