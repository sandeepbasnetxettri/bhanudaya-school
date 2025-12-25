<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Structure - Bhanudaya Secondary School</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Additional styles specific to fee structure page */
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px) translateZ(0);
            }
            to {
                opacity: 1;
                transform: translateY(0) translateZ(0);
            }
        }
        
        @keyframes float {
            0% {
                transform: translateY(0) translateZ(0);
            }
            50% {
                transform: translateY(-10px) translateZ(10px);
            }
            100% {
                transform: translateY(0) translateZ(0);
            }
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(156, 39, 176, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(156, 39, 176, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(156, 39, 176, 0);
            }
        }
        
        @keyframes glow {
            0% {
                box-shadow: 0 0 5px rgba(33, 150, 243, 0.5);
            }
            50% {
                box-shadow: 0 0 20px rgba(33, 150, 243, 0.8);
            }
            100% {
                box-shadow: 0 0 5px rgba(33, 150, 243, 0.5);
            }
        }
        
        .refund-policy.expanded,
        .fee-highlight.expanded,
        .important-note.expanded {
            transform: translateY(-10px) translateZ(30px) rotateX(5deg);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .refund-policy.expanded ul li,
        .fee-highlight.expanded p,
        .important-note.expanded ul li {
            transform: translateZ(20px);
        }
        
        .fee-highlight {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            transition: all 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform-style: preserve-3d;
            position: relative;
            perspective: 1000px;
        }
        
        .fee-highlight:hover {
            transform: translateY(-5px) translateZ(15px) rotateX(5deg);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .fee-highlight h2, .fee-highlight h3 {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0);
        }
        
        .fee-highlight:hover h2, .fee-highlight:hover h3 {
            transform: translateY(-3px) translateZ(10px);
        }
        
        .fee-highlight p {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0);
        }
        
        .fee-highlight:hover p {
            transform: translateY(-2px) translateZ(5px);
        }
        
        .payment-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
            perspective: 1200px;
        }
        
        .payment-card {
            background: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform-style: preserve-3d;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .payment-card:hover {
            transform: translateY(-20px) translateZ(40px) rotateX(10deg) rotateY(8deg);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }
        
        .payment-card i {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 15px;
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0) rotateX(0deg);
            position: relative;
            perspective: 1000px;
            transform-style: preserve-3d;
        }
        
        .payment-card:hover i {
            transform: translateY(-10px) translateZ(20px) rotateX(10deg) rotateY(5deg);
            text-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .payment-card h3 {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0);
            position: relative;
            perspective: 1000px;
        }
        
        .payment-card:hover h3 {
            transform: translateY(-5px) translateZ(15px);
        }
        
        .payment-card p {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0);
            position: relative;
            perspective: 1000px;
        }
        
        .payment-card:hover p {
            transform: translateY(-3px) translateZ(10px);
        }
        
        .important-note {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            transition: all 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform-style: preserve-3d;
            position: relative;
            perspective: 1000px;
        }
        
        .important-note:hover {
            transform: translateY(-5px) translateZ(15px) rotateX(5deg);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .important-note h3 {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0);
        }
        
        .important-note:hover h3 {
            transform: translateY(-3px) translateZ(10px);
        }
        
        .important-note ul li {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0);
        }
        
        .important-note:hover ul li {
            transform: translateX(5px) translateZ(5px);
        }
        
        /* Fee Structure Table Enhancements */
        .fee-structure-table {
            transform-style: preserve-3d;
            perspective: 1500px;
            transition: all 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        .fee-structure-table:hover {
            transform: translateY(-10px) translateZ(30px);
        }
        
        .fee-structure-table th {
            transform: translateZ(10px);
            position: relative;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            animation: headerGlow 3s ease-in-out infinite alternate;
        }
        
        @keyframes headerGlow {
            0% {
                box-shadow: 0 0 5px rgba(102, 126, 234, 0.5);
            }
            100% {
                box-shadow: 0 0 20px rgba(102, 126, 234, 0.8);
            }
        }
        
        @keyframes bounce {
            0%, 20%, 60%, 100% {
                transform: translateY(0) translateZ(0);
            }
            40% {
                transform: translateY(-20px) translateZ(20px);
            }
            80% {
                transform: translateY(-10px) translateZ(10px);
            }
        }
        
        .fee-structure-table tr {
            transition: all 0.3s ease;
            transform-style: preserve-3d;
        }
        
        .fee-structure-table tr:hover {
            transform: translateZ(20px) translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        
        .fee-structure-table td {
            transition: all 0.3s ease;
            transform-style: preserve-3d;
        }
        
        .fee-structure-table tr:hover td {
            transform: translateZ(15px);
        }
        
        .fee-structure-table td strong {
            transition: all 0.3s ease;
        }
        
        .fee-structure-table tr:hover td strong {
            transform: translateZ(25px);
            text-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }
        
        /* Special effect for total fee cells */
        .fee-structure-table td:last-child {
            font-weight: bold;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 5px;
            transform: translateZ(5px);
            transition: all 0.3s ease;
        }
        
        .fee-structure-table tr:hover td:last-child {
            transform: translateZ(30px);
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
        }
        
        .refund-policy {
            background-color: #f3e5f5;
            border-left: 4px solid #9c27b0;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            transition: all 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform-style: preserve-3d;
            position: relative;
            perspective: 1000px;
        }
        
        .refund-policy:hover {
            transform: translateY(-5px) translateZ(15px) rotateX(5deg);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .refund-policy h3 {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0);
        }
        
        .refund-policy:hover h3 {
            transform: translateY(-3px) translateZ(10px);
        }
        
        .refund-policy ul li {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0);
        }
        
        .refund-policy:hover ul li {
            transform: translateX(5px) translateZ(5px);
        }
        
        .discount-section {
            margin: 40px 0;
        }
        
        .discount-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
            perspective: 1200px;
        }
        
        .discount-card {
            background: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform-style: preserve-3d;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .discount-card:hover {
            transform: translateY(-20px) translateZ(40px) rotateX(10deg) rotateY(8deg);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }
        
        .discount-card i {
            font-size: 2rem;
            color: var(--success-color);
            margin-bottom: 15px;
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0) rotateX(0deg);
            position: relative;
            perspective: 1000px;
            transform-style: preserve-3d;
        }
        
        .discount-card:hover i {
            transform: translateY(-10px) translateZ(20px) rotateX(10deg) rotateY(5deg);
            text-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .discount-card h3 {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0);
            position: relative;
            perspective: 1000px;
        }
        
        .discount-card:hover h3 {
            transform: translateY(-5px) translateZ(15px);
        }
        
        .discount-card p {
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateZ(0);
            position: relative;
            perspective: 1000px;
        }
        
        .discount-card:hover p {
            transform: translateY(-3px) translateZ(10px);
        }
    </style>
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
                    <li><a href="notice.php">Notice Board</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <section class="page-banner">
        <div class="container">
            <h1 style="transform: translateZ(20px); transition: all 0.5s ease;">School Fee Structure</h1>
            <p style="transform: translateZ(10px); transition: all 0.5s ease;"><a href="../index.php">Home</a> / <a href="admission.php">Admissions</a> / Fee Structure</p>
        </div>
    </section>

    <section class="page-content">
        <div class="container" style="transform-style: preserve-3d; perspective: 1500px; animation: fadeInUp 1s ease-out;">
            <div class="fee-highlight">
                <h2><i class="fas fa-info-circle"></i> Important Notice</h2>
                <p>All fees are annual and subject to revision. Late payment may incur additional charges as per school policy.</p>
            </div>

            <h2 class="section-title" id="feeStructureTitle">Comprehensive Fee Structure (Annual)</h2>
            
            <div class="table-responsive">
                <table class="fee-structure-table">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Admission Fee</th>
                            <th>Tuition Fee</th>
                            <th>Other Fees</th>
                            <th>Total Annual Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Primary (1-5)</td>
                            <td>NPR 2,000</td>
                            <td>NPR 3,000</td>
                            <td>NPR 500</td>
                            <td><strong>NPR 5,500</strong></td>
                        </tr>
                        <tr>
                            <td>Lower Secondary (6-8)</td>
                            <td>NPR 2,000</td>
                            <td>NPR 5,000</td>
                            <td>NPR 500</td>
                            <td><strong>NPR 7,500</strong></td>
                        </tr>
                        <tr>
                            <td>Secondary (9-10)</td>
                            <td>NPR 2,000</td>
                            <td>NPR 5,000</td>
                            <td>NPR 500</td>
                            <td><strong>NPR 7,500</strong></td>
                        </tr>
                        <tr>
                            <td>+2 Computer Science</td>
                            <td>NPR 3,000</td>
                            <td>NPR 7,000</td>
                            <td>NPR 2,000</td>
                            <td><strong>NPR 12,000</strong></td>
                        </tr>
                        <tr>
                            <td>+2 Hotel Management</td>
                            <td>NPR 3,000</td>
                            <td>NPR 7,000</td>
                            <td>NPR 2,000</td>
                            <td><strong>NPR 12,000</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="important-note" id="otherFeesSection" style="animation: glow 3s ease-in-out infinite;">
                <h3><i class="fas fa-exclamation-triangle"></i> Other Important Fees</h3>
                <ul>
                    <li style="transition: all 0.3s ease; transform-style: preserve-3d; cursor: pointer;" onmouseover="this.style.transform='translateZ(15px)'; this.style.textShadow='0 5px 10px rgba(0,0,0,0.2)';" onmouseout="this.style.transform='translateZ(0)'; this.style.textShadow='none';"><strong>Annual Examination Fee:</strong> NPR 500 (per student)</li>
                    <li style="transition: all 0.3s ease; transform-style: preserve-3d; cursor: pointer;" onmouseover="this.style.transform='translateZ(15px)'; this.style.textShadow='0 5px 10px rgba(0,0,0,0.2)';" onmouseout="this.style.transform='translateZ(0)'; this.style.textShadow='none';"><strong>Library Fee:</strong> NPR 300 (per annum)</li>
                    <li style="transition: all 0.3s ease; transform-style: preserve-3d; cursor: pointer;" onmouseover="this.style.transform='translateZ(15px)'; this.style.textShadow='0 5px 10px rgba(0,0,0,0.2)';" onmouseout="this.style.transform='translateZ(0)'; this.style.textShadow='none';"><strong>Computer Lab Fee:</strong> NPR 500 (per annum)</li>
                    <li style="transition: all 0.3s ease; transform-style: preserve-3d; cursor: pointer;" onmouseover="this.style.transform='translateZ(15px)'; this.style.textShadow='0 5px 10px rgba(0,0,0,0.2)';" onmouseout="this.style.transform='translateZ(0)'; this.style.textShadow='none';"><strong>Transportation Fee:</strong> As per distance (contact office for details)</li>
                    <li style="transition: all 0.3s ease; transform-style: preserve-3d; cursor: pointer;" onmouseover="this.style.transform='translateZ(15px)'; this.style.textShadow='0 5px 10px rgba(0,0,0,0.2)';" onmouseout="this.style.transform='translateZ(0)'; this.style.textShadow='none';"><strong>Uniform & Books:</strong> As per actual cost</li>
                </ul>
            </div>

            <h2 class="section-title">Payment Options</h2>
            <div class="payment-options">
                <div class="payment-card">
                    <i class="fas fa-money-bill-wave"></i>
                    <h3>Cash Payment</h3>
                    <p>Pay directly at the school office during working hours</p>
                </div>
                <div class="payment-card">
                    <i class="fas fa-university"></i>
                    <h3>Bank Transfer</h3>
                    <p>Transfer to our designated bank account</p>
                </div>
                <div class="payment-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Digital Payment</h3>
                    <p>ESewa: 9768827327, Khalti, or other digital wallets</p>
                </div>
            </div>

            <div class="discount-section">
                <h2 class="section-title">Discounts & Scholarships</h2>
                <div class="discount-grid">
                    <div class="discount-card">
                        <i class="fas fa-trophy"></i>
                        <h3>Merit-Based Scholarship</h3>
                        <p>Up to 50% fee waiver for students scoring above 90% in previous examinations.</p>
                    </div>
                    <div class="discount-card">
                        <i class="fas fa-hands-helping"></i>
                        <h3>Need-Based Scholarship</h3>
                        <p>Financial assistance for economically disadvantaged students. 25-75% coverage based on family income.</p>
                    </div>
                    <div class="discount-card">
                        <i class="fas fa-running"></i>
                        <h3>Sports Scholarship</h3>
                        <p>Up to 40% discount for students with exceptional sports achievements at district/national level.</p>
                    </div>
                    <div class="discount-card">
                        <i class="fas fa-child"></i>
                        <h3>Sibling Discount</h3>
                        <p>20% discount on tuition fees for second child, 30% for third child onwards.</p>
                    </div>
                </div>
            </div>

            <div class="refund-policy" style="cursor: pointer; animation: pulse 2s infinite;" onclick="this.classList.toggle('expanded')">
                <h3><i class="fas fa-undo"></i> Refund Policy</h3>
                <ul>
                    <li style="transition: all 0.3s ease; transform-style: preserve-3d;">Admission fees are non-refundable under any circumstances</li>
                    <li style="transition: all 0.3s ease; transform-style: preserve-3d;">Tuition fees can be refunded only if a student leaves the school before the commencement of classes, subject to deduction of administrative charges</li>
                    <li style="transition: all 0.3s ease; transform-style: preserve-3d;">No refund will be processed after 30 days from the start of the academic year</li>
                    <li style="transition: all 0.3s ease; transform-style: preserve-3d;">Refund requests must be submitted in writing to the principal with valid reasons</li>
                </ul>
            </div>

            <div class="fee-highlight" style="cursor: pointer;" onclick="this.classList.toggle('expanded')">
                <h3><i class="fas fa-calendar-alt"></i> Payment Schedule</h3>
                <p style="transition: all 0.3s ease; transform-style: preserve-3d;"><strong>First Installment:</strong> Before the start of the academic year (Ashad/Baisakh)</p>
                <p style="transition: all 0.3s ease; transform-style: preserve-3d;"><strong>Second Installment:</strong> By Magh/Shrawan</p>
                <p style="transition: all 0.3s ease; transform-style: preserve-3d;"><strong>Third Installment:</strong> By Falgun/Chaitra</p>
                <p style="transition: all 0.3s ease; transform-style: preserve-3d;">Late payments will incur a penalty of 2% per month.</p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>About Us</h3>
                    <p>Bhanudaya Secondary School is committed to providing quality education and nurturing young minds for a successful future.</p>
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
                        <li><i class="fas fa-phone"></i> +977-78-620134</li>
                        <li><i class="fas fa-envelope"></i> bhanudayahss071@gmail.com</li>
                        <li><i class="fas fa-clock"></i> Sun-Fri: 9:00 AM - 4:00 PM</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="../js/main.js"></script>
    <script>
        // Add 3D tilt effect to cards
        document.addEventListener('DOMContentLoaded', function() {
            // Add mouse move listeners for 3D tilt effects
            const cards = document.querySelectorAll('.payment-card, .discount-card');
            
            cards.forEach(card => {
                // Store original transform
                const originalTransform = 'translateY(-15px) translateZ(30px) rotateX(8deg) rotateY(5deg)';
                
                card.addEventListener('mousemove', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const rotateY = ((x - centerX) / centerX) * 5; // Max 5 degrees
                    const rotateX = ((centerY - y) / centerY) * 5; // Max 5 degrees
                    
                    this.style.transform = `translateY(-15px) translateZ(30px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = originalTransform;
                });
                
                // Add click effect
                card.addEventListener('mousedown', function() {
                    this.style.transform = 'translateY(-5px) translateZ(20px) rotateX(5deg) rotateY(3deg)';
                });
                
                card.addEventListener('mouseup', function() {
                    this.style.transform = originalTransform;
                });
            });
            
            // Add subtle animation to elements on page load
            const highlights = document.querySelectorAll('.fee-highlight, .important-note, .refund-policy');
            highlights.forEach((element, index) => {
                // Apply initial styles
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.transition = 'all 0.6s ease';
                
                // Animate in with delay
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 150);
                
                // Add special effect for refund policy and payment schedule
                if (element.classList.contains('refund-policy') || 
                    (element.querySelector('h3 i.fa-calendar-alt'))) {
                    element.style.animation = 'float 3s ease-in-out infinite';
                }
                            
                // Add special 3D effects for Other Important Fees section
                if (element.id === 'otherFeesSection') {
                    element.style.cursor = 'pointer';
                                
                    // Add special entrance animation
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(30px) translateZ(-20px)';
                    setTimeout(() => {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0) translateZ(0)';
                                    
                        // Add a subtle highlight effect after entrance
                        setTimeout(() => {
                            element.style.animation = 'glow 3s ease-in-out infinite';
                        }, 500);
                    }, 1000);
                }
                            
                // Add special 3D effects for fee structure title
                if (element.id === 'feeStructureTitle') {
                    element.style.cursor = 'pointer';
                                
                    element.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-10px) translateZ(30px)';
                        this.style.textShadow = '0 10px 20px rgba(0, 0, 0, 0.3)';
                    });
                                
                    element.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) translateZ(0)';
                        this.style.textShadow = 'none';
                    });
                                
                    element.addEventListener('click', function() {
                        // Add a bounce effect
                        this.style.animation = 'bounce 0.6s';
                    });
                }
                            
                // Add special 3D effects for fee structure table
                if (element.classList.contains('fee-structure-table')) {
                    // Add entrance animation
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(50px) translateZ(-30px)';
                    setTimeout(() => {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0) translateZ(0)';
                    }, 800);
                                
                    element.addEventListener('mousemove', function(e) {
                        const rect = this.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                                    
                        const centerX = rect.width / 2;
                        const centerY = rect.height / 2;
                                    
                        const rotateY = ((x - centerX) / centerX) * 5; // Max 5 degrees
                        const rotateX = ((centerY - y) / centerY) * 5; // Max 5 degrees
                                    
                        this.style.transform = `perspective(1500px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                    });
                                
                    element.addEventListener('mouseleave', function() {
                        this.style.transform = 'perspective(1500px) rotateX(0) rotateY(0)';
                    });
                                
                    // Add row highlighting effect
                    const rows = this.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        row.addEventListener('mouseenter', function() {
                            this.style.transform = 'translateZ(25px) translateY(-5px)';
                            this.style.boxShadow = '0 15px 30px rgba(0, 0, 0, 0.2)';
                                        
                            // Highlight total fee column
                            const totalCell = this.cells[this.cells.length - 1];
                            totalCell.style.transform = 'translateZ(35px)';
                            totalCell.style.textShadow = '0 5px 15px rgba(0, 0, 0, 0.3)';
                        });
                                    
                        row.addEventListener('mouseleave', function() {
                            this.style.transform = 'translateZ(0) translateY(0)';
                            this.style.boxShadow = 'none';
                                        
                            const totalCell = this.cells[this.cells.length - 1];
                            totalCell.style.transform = 'translateZ(0)';
                            totalCell.style.textShadow = 'none';
                        });
                    });
                }
                                
                    // Add click to expand effect
                    element.addEventListener('click', function() {
                        this.classList.toggle('expanded');
                                    
                        // Add 3D pop effect
                        const originalTransform = this.style.transform;
                        this.style.transform = originalTransform + ' scale(1.02) translateZ(20px)';
                        setTimeout(() => {
                            this.style.transform = originalTransform + ' translateZ(10px)';
                        }, 150);
                    });
                                
                    // Add enhanced hover effects
                    element.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-8px) translateZ(25px) rotateX(6deg)';
                                    
                        // Add 3D effect to fee items
                        const feeItems = this.querySelectorAll('li');
                        feeItems.forEach((item, index) => {
                            setTimeout(() => {
                                item.style.transform = 'translateZ(20px)';
                                item.style.textShadow = '0 5px 10px rgba(0, 0, 0, 0.2)';
                            }, index * 75);
                        });
                    });
                                
                    element.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('expanded')) {
                            this.style.transform = 'translateY(0) translateZ(0)';
                        } else {
                            this.style.transform = 'translateZ(10px)';
                        }
                                    
                        // Reset fee items
                        const feeItems = this.querySelectorAll('li');
                        feeItems.forEach(item => {
                            item.style.transform = 'translateZ(0)';
                            item.style.textShadow = 'none';
                        });
                    });
                }
                
                // Add click effect for 3D expansion
                element.addEventListener('click', function() {
                    this.classList.toggle('expanded');
                                
                    // Add 3D pop effect
                    const originalTransform = this.style.transform;
                    this.style.transform = originalTransform + ' scale(1.02)';
                    setTimeout(() => {
                        this.style.transform = originalTransform;
                    }, 150);
                });
                
                // Add mouse enter/leave effects
                element.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) translateZ(20px)';
                    
                    // Add 3D effect to child elements
                    const listItems = this.querySelectorAll('li');
                    const paragraphs = this.querySelectorAll('p');
                    
                    listItems.forEach((item, index) => {
                        setTimeout(() => {
                            item.style.transform = 'translateZ(15px)';
                        }, index * 50);
                    });
                    
                    paragraphs.forEach((para, index) => {
                        setTimeout(() => {
                            para.style.transform = 'translateZ(15px)';
                        }, index * 50);
                    });
                });
                
                element.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('expanded')) {
                        this.style.transform = 'translateY(0) translateZ(0)';
                    }
                    
                    // Reset child elements
                    const listItems = this.querySelectorAll('li');
                    const paragraphs = this.querySelectorAll('p');
                    
                    listItems.forEach(item => {
                        item.style.transform = 'translateZ(0)';
                    });
                    
                    paragraphs.forEach(para => {
                        para.style.transform = 'translateZ(0)';
                    });
                });
            });
            
            // Add 3D effect to table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateZ(15px)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateZ(0)';
                });
            });
            
            // Add parallax effect to banner
            const banner = document.querySelector('.page-banner');
            document.addEventListener('mousemove', function(e) {
                const x = (window.innerWidth / 2 - e.clientX) / 25;
                const y = (window.innerHeight / 2 - e.clientY) / 25;
                banner.style.transform = `translate3d(${x}px, ${y}px, 0)`;
            });
            
            // Add subtle floating animation to icons
            const icons = document.querySelectorAll('.payment-card i, .discount-card i');
            icons.forEach(icon => {
                icon.style.animation = 'float 3s ease-in-out infinite';
            });
        });
    </script>
</body>
</html>