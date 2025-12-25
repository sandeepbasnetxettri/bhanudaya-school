<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Excellence School</title>
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
                    <li><a href="contact.php" class="active">Contact</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="page-banner">
        <div class="container">
            <h1>Contact Us</h1>
            <p><a href="../index.php">Home</a> / Contact</p>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="contact-info-section">
                <div class="contact-info-grid">
                    <div class="contact-info-card">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>Address</h3>
                        <p>Binayi Triveni Rural Municipality <br>Ward No. 1, Dumkibas, Nawalaparasi<br>Nepal</p>
                    </div>
                    <div class="contact-info-card">
                        <i class="fas fa-phone"></i>
                        <h3>Phone</h3>
                        <p>Office: +977-1-4567890<br>Principal: 9768827327 <br>Admission:+977-78-620134 </p>
                    </div>
                    <div class="contact-info-card">
                        <i class="fas fa-envelope"></i>
                        <h3>Email</h3>
                        <p>kewalthanait@gmail.com<br>bhanudayahss071@gmail.com<br>sandeepbasnetxettri@gmail.com</p>
                    </div>
                    <div class="contact-info-card">
                        <i class="fas fa-clock"></i>
                        <h3>Office Hours</h3>
                        <p>Sunday - Friday<br>9:00 AM - 4:00 PM<br>Saturday: Closed</p>
                    </div>
                </div>
            </div>

            <div class="contact-form-map">
                <div class="contact-form-section">
                    <h2>Send Us a Message</h2>
                    <form action="https://api.web3forms.com/submit" method="POST" id="contactForm">
                            <div class="form-row">
                            <div class="form-group">
                                <label>Full Name <span style="color: red;">*</span></label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Email <span style="color: red;">*</span></label>
                                <input type="email" name="email" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Phone Number <span style="color: red;">*</span></label>
                                <input type="tel" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label>Subject <span style="color: red;">*</span></label>
                                <select name="subject" required>
                                    <option value="">Select Subject</option>
                                    <option value="admission">Admission Inquiry</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="complaint">Complaint</option>
                                    <option value="feedback">Feedback</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Message <span style="color: red;">*</span></label>
                            <textarea name="message" rows="6" required></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    <form id="contactForm">
                </div>

                <div class="map-section">
                    <h2>Find Us on Map</h2>
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3536.0750198524906!2d83.86406701117552!3d27.59120367615034!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399439e7c5181149%3A0x1869cd59b3c0601b!2sbhanudaya%20secondary%20school!5e0!3m2!1sen!2snp!4v1765853483227!5m2!1sen!2snp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>" 
                    </div>
                </div>
            </div>

            <div class="faq-section">
                <h2>Frequently Asked Questions</h2>
                <div class="faq-list">
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>What are the admission timings?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Admissions are typically open from January to March for the upcoming academic year. However, we may accept applications throughout the year based on seat availability.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Do you provide transportation facilities?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, we provide bus transportation services covering major areas of Kathmandu valley. Transportation fees are separate from tuition fees.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Is there a hostel facility available?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Currently, we don't have an on-campus hostel facility. However, we can recommend nearby hostel accommodations for out-of-district students.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>What extra-curricular activities do you offer?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We offer various extra-curricular activities including sports (football, basketball, volleyball), music, dance, art, robotics, debate, and cultural programs.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>How can I check my child's progress?</h4>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Parents can access the student portal using their child's login credentials to check attendance, results, homework, and communicate with teachers.</p>
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
    <script src="../js/contact.js"></script>
</body>
</html>
