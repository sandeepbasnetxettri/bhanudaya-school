<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Board - Excellence School</title>
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
            <div class="container">
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
                        <a href="#">Academics <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="courses.php">Courses Offered</a></li>
                            <li><a href="computer-science.php">+2 Computer Science</a></li>
                            <li><a href="hotel-management.php">+2 Hotel Management</a></li>
                            <li><a href="timetable.php">Class Timetable</a></li>
                            <li><a href="calendar.php">Academic Calendar</a></li>
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
            </div>
        </nav>
    </header>

    <section class="page-banner">
        <div class="container">
            <h1>Notice Board</h1>
            <p><a href="../index.php">Home</a> / Notice Board</p>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="notice-filter">
                <h3>Filter by Category:</h3>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-type="all">All</button>
                    <button class="filter-btn" data-type="Notice">Notices</button>
                    <button class="filter-btn" data-type="Event">Events</button>
                    <button class="filter-btn" data-type="Achievement">Achievements</button>
                    <button class="filter-btn" data-type="Holiday">Holidays</button>
                </div>
            </div>

            <div class="notices-container" id="noticeBoard">
                <!-- Notices loaded dynamically -->
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
    <script src="../js/notice.js"></script>
</body>
</html>
