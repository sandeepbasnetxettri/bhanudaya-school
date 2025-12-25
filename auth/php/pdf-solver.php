<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['user_name'];

// Handle PDF upload and processing
$message = '';
$error = '';

if (isset($_POST['process_pdf'])) {
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $fileExtension = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
        
        if ($fileExtension === 'pdf') {
            $uploadDir = '../../uploads/pdf_solver/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['pdf_file']['name']);
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $uploadPath)) {
                // In a real implementation, you would process the PDF here
                // For now, we'll just simulate processing
                $message = "PDF uploaded successfully. In a full implementation, this would process the PDF for homework solutions.";
                
                // Simulate some processing results
                $processingResults = [
                    'file_name' => $fileName,
                    'page_count' => rand(1, 20),
                    'text_extracted' => true,
                    'problems_identified' => rand(1, 10),
                    'solutions_generated' => rand(1, 5)
                ];
            } else {
                $error = "Failed to upload PDF file.";
            }
        } else {
            $error = "Invalid file type. Only PDF files are allowed.";
        }
    } else {
        $error = "Please select a PDF file to upload.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Solver - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/portal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .editor-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .editor-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .upload-area {
            border: 2px dashed #4CAF50;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 30px;
            background-color: #f9f9f9;
        }
        
        .upload-area i {
            font-size: 3rem;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        
        .upload-area h3 {
            margin: 0 0 10px 0;
        }
        
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            overflow: hidden;
            margin: 20px 0;
        }
        
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        
        .btn-file {
            padding: 12px 24px;
            background: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
        }
        
        .btn-file:hover {
            background: #45a049;
        }
        
        .file-name {
            margin-top: 10px;
            font-style: italic;
            color: #666;
        }
        
        .results-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-top: 30px;
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .result-card {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        
        .result-card h4 {
            margin: 0 0 10px 0;
            color: #4CAF50;
        }
        
        .result-card p {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .btn-process {
            display: block;
            width: 100%;
            padding: 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 20px;
        }
        
        .btn-process:hover {
            background: #45a049;
        }
        
        @media (max-width: 768px) {
            .editor-container {
                padding: 10px;
            }
            
            .results-grid {
                grid-template-columns: 1fr;
            }
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
                <div class="top-links">
                    <a href="teacher-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="homework-management.php"><i class="fas fa-book"></i> Homework</a>
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
                            <p class="tagline"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="portal-header">
        <div class="container">
            <h1><i class="fas fa-file-pdf"></i> PDF Solver</h1>
            <p>Upload PDF homework assignments for automated solution processing</p>
        </div>
    </section>

    <section class="portal-content">
        <div class="editor-container">
            <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <div class="editor-header">
                <h1>Upload Homework PDF</h1>
                <p>Upload a PDF containing homework problems for automated solution processing</p>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="upload-area">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h3>Drag & Drop PDF File</h3>
                    <p>or</p>
                    
                    <div class="file-input-wrapper">
                        <input type="file" id="pdf_file" name="pdf_file" accept=".pdf" required>
                        <span class="btn-file">Browse Files</span>
                    </div>
                    
                    <div class="file-name" id="file-name">No file selected</div>
                    
                    <p>Supported format: PDF only</p>
                </div>
                
                <button type="submit" name="process_pdf" class="btn-process">
                    <i class="fas fa-cogs"></i> Process PDF
                </button>
            </form>
            
            <?php if (isset($processingResults)): ?>
            <div class="results-section">
                <h2>Processing Results</h2>
                <p>PDF "<?php echo htmlspecialchars($processingResults['file_name']); ?>" has been processed successfully.</p>
                
                <div class="results-grid">
                    <div class="result-card">
                        <h4>Pages</h4>
                        <p><?php echo $processingResults['page_count']; ?></p>
                    </div>
                    <div class="result-card">
                        <h4>Text Extracted</h4>
                        <p><?php echo $processingResults['text_extracted'] ? 'Yes' : 'No'; ?></p>
                    </div>
                    <div class="result-card">
                        <h4>Problems Found</h4>
                        <p><?php echo $processingResults['problems_identified']; ?></p>
                    </div>
                    <div class="result-card">
                        <h4>Solutions Generated</h4>
                        <p><?php echo $processingResults['solutions_generated']; ?></p>
                    </div>
                </div>
                
                <div style="margin-top: 20px; text-align: center;">
                    <p>In a full implementation, this would provide:</p>
                    <ul style="display: inline-block; text-align: left;">
                        <li>Automated problem identification</li>
                        <li>Step-by-step solution generation</li>
                        <li>Exportable solution documents</li>
                        <li>Integration with homework assignments</li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
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

    <script>
        // File name display
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('pdf_file');
            const fileName = document.getElementById('file-name');
            
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileName.textContent = this.files[0].name;
                } else {
                    fileName.textContent = 'No file selected';
                }
            });
        });
    </script>
</body>
</html>