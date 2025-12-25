<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Database connection
require_once '../../config/dbconnection.php';

// Handle form submissions
$message = '';
$error = '';

// Handle notice creation
if (isset($_POST['create_notice'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $noticeType = $_POST['notice_type'];
    $targetAudience = isset($_POST['target_audience']) ? $_POST['target_audience'] : [];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    
    // Validate input
    if (empty($title) || empty($content) || empty($noticeType)) {
        $error = "Title, content, and notice type are required.";
    } else {
        try {
            // Convert target audience array to JSON
            $targetAudienceJson = json_encode($targetAudience);
            
            // Create new notice
            $stmt = $pdo->prepare("INSERT INTO notices (title, content, notice_type, posted_by, target_audience, start_date, end_date, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $content, $noticeType, $_SESSION['user_id'], $targetAudienceJson, $startDate, $endDate, $isPublished]);
            
            $message = "Notice created successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle notice updates
if (isset($_POST['update_notice'])) {
    $noticeId = (int)$_POST['notice_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $noticeType = $_POST['notice_type'];
    $targetAudience = isset($_POST['target_audience']) ? $_POST['target_audience'] : [];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    
    // Validate input
    if (empty($title) || empty($content) || empty($noticeType)) {
        $error = "Title, content, and notice type are required.";
    } else {
        try {
            // Convert target audience array to JSON
            $targetAudienceJson = json_encode($targetAudience);
            
            // Update notice
            $stmt = $pdo->prepare("UPDATE notices SET title = ?, content = ?, notice_type = ?, target_audience = ?, start_date = ?, end_date = ?, is_published = ? WHERE id = ?");
            $stmt->execute([$title, $content, $noticeType, $targetAudienceJson, $startDate, $endDate, $isPublished, $noticeId]);
            
            $message = "Notice updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle notice deletion
if (isset($_POST['delete_notice'])) {
    $noticeId = (int)$_POST['notice_id'];
    
    try {
        // Delete notice
        $stmt = $pdo->prepare("DELETE FROM notices WHERE id = ?");
        $stmt->execute([$noticeId]);
        
        $message = "Notice deleted successfully.";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch all notices for display
try {
    $stmt = $pdo->query("SELECT id, title, notice_type, target_audience, start_date, end_date, is_published, created_at FROM notices ORDER BY created_at DESC");
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $notices = [];
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
    <title>Notice Board Management - Excellence School</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .editor-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .editor-header h1 {
            margin: 0;
        }
        
        .notice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .notice-table th,
        .notice-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .notice-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }
        
        .notice-table tr:last-child td {
            border-bottom: none;
        }
        
        .notice-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-small {
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 10px;
            width: 90%;
            max-width: 700px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h2 {
            margin: 0;
            color: #333;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }
        
        .close-modal:hover {
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-col {
            flex: 1;
        }
        
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
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
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-published {
            background-color: #4CAF50;
            color: white;
        }
        
        .status-draft {
            background-color: #ffc107;
            color: black;
        }
        
        @media (max-width: 768px) {
            .editor-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .notice-table {
                font-size: 0.9rem;
            }
            
            .notice-table th,
            .notice-table td {
                padding: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn-small {
                width: 100%;
                text-align: center;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .modal-content {
                max-width: 95%;
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

                </div>
            </div>
        </div>

    </header>

    <section class="admin-header">
        <div class="container">
            <h1><i class="fas fa-bell"></i> Notice Board Management</h1>
            <p>Create and manage school notices</p>
        </div>
    </section>

    <section class="admin-content">
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
                <h1>Notice Records</h1>
                <button id="showCreateModal" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Notice
                </button>
            </div>
            
            <table class="notice-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Audience</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notices as $notice): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($notice['id']); ?></td>
                        <td><?php echo htmlspecialchars(substr($notice['title'], 0, 30)) . (strlen($notice['title']) > 30 ? '...' : ''); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($notice['notice_type'])); ?></td>
                        <td>
                            <?php 
                            $audience = json_decode($notice['target_audience'], true);
                            if (is_array($audience)) {
                                echo implode(', ', array_map('ucfirst', $audience));
                            } else {
                                echo 'All';
                            }
                            ?>
                        </td>
                        <td><?php echo $notice['start_date'] ? date('M j, Y', strtotime($notice['start_date'])) : 'N/A'; ?></td>
                        <td><?php echo $notice['end_date'] ? date('M j, Y', strtotime($notice['end_date'])) : 'N/A'; ?></td>
                        <td>
                            <span class="status-badge <?php echo $notice['is_published'] ? 'status-published' : 'status-draft'; ?>">
                                <?php echo $notice['is_published'] ? 'Published' : 'Draft'; ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($notice['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-outline btn-small edit-notice" 
                                        data-id="<?php echo $notice['id']; ?>"
                                        data-title="<?php echo htmlspecialchars($notice['title']); ?>"
                                        data-content="<?php echo htmlspecialchars($notice['content'] ?? ''); ?>"
                                        data-type="<?php echo htmlspecialchars($notice['notice_type']); ?>"
                                        data-audience="<?php echo htmlspecialchars($notice['target_audience']); ?>"
                                        data-start="<?php echo htmlspecialchars($notice['start_date'] ?? ''); ?>"
                                        data-end="<?php echo htmlspecialchars($notice['end_date'] ?? ''); ?>"
                                        data-published="<?php echo $notice['is_published']; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline btn-small delete-notice" 
                                        data-id="<?php echo $notice['id']; ?>"
                                        data-title="<?php echo htmlspecialchars($notice['title']); ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Create Notice Modal -->
    <div id="createNoticeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-bell"></i> Add New Notice</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="createNoticeForm" action="" method="POST">
                <div class="form-group">
                    <label for="create_title">Title *</label>
                    <input type="text" id="create_title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="create_content">Content *</label>
                    <textarea id="create_content" name="content" class="form-control" rows="5" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_notice_type">Notice Type *</label>
                            <select id="create_notice_type" name="notice_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="academic">Academic</option>
                                <option value="administrative">Administrative</option>
                                <option value="event">Event</option>
                                <option value="holiday">Holiday</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label>Target Audience</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="create_audience_students" name="target_audience[]" value="students">
                                    <label for="create_audience_students">Students</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="create_audience_teachers" name="target_audience[]" value="teachers">
                                    <label for="create_audience_teachers">Teachers</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="create_audience_parents" name="target_audience[]" value="parents">
                                    <label for="create_audience_parents">Parents</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="create_audience_all" name="target_audience[]" value="all">
                                    <label for="create_audience_all">All</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_start_date">Start Date</label>
                            <input type="date" id="create_start_date" name="start_date" class="form-control">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_end_date">End Date</label>
                            <input type="date" id="create_end_date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="create_is_published" name="is_published" value="1" checked>
                        <label for="create_is_published">Publish Notice</label>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="create_notice" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Notice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Notice Modal -->
    <div id="editNoticeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Notice</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="editNoticeForm" action="" method="POST">
                <input type="hidden" id="edit_notice_id" name="notice_id">
                <div class="form-group">
                    <label for="edit_title">Title *</label>
                    <input type="text" id="edit_title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_content">Content *</label>
                    <textarea id="edit_content" name="content" class="form-control" rows="5" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_notice_type">Notice Type *</label>
                            <select id="edit_notice_type" name="notice_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="academic">Academic</option>
                                <option value="administrative">Administrative</option>
                                <option value="event">Event</option>
                                <option value="holiday">Holiday</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label>Target Audience</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="edit_audience_students" name="target_audience[]" value="students">
                                    <label for="edit_audience_students">Students</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="edit_audience_teachers" name="target_audience[]" value="teachers">
                                    <label for="edit_audience_teachers">Teachers</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="edit_audience_parents" name="target_audience[]" value="parents">
                                    <label for="edit_audience_parents">Parents</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="edit_audience_all" name="target_audience[]" value="all">
                                    <label for="edit_audience_all">All</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_start_date">Start Date</label>
                            <input type="date" id="edit_start_date" name="start_date" class="form-control">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_end_date">End Date</label>
                            <input type="date" id="edit_end_date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="edit_is_published" name="is_published" value="1">
                        <label for="edit_is_published">Publish Notice</label>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="update_notice" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Notice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteNoticeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete notice <strong id="deleteNoticeTitle"></strong>? This action cannot be undone.</p>
            </div>
            <form id="deleteNoticeForm" action="" method="POST">
                <input type="hidden" id="delete_notice_id" name="notice_id">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="delete_notice" class="btn btn-primary" style="background-color: #dc3545;">
                        <i class="fas fa-trash"></i> Delete Notice
                    </button>
                </div>
            </form>
        </div>
    </div>

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
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Get modal elements
            const createNoticeModal = document.getElementById('createNoticeModal');
            const editNoticeModal = document.getElementById('editNoticeModal');
            const deleteNoticeModal = document.getElementById('deleteNoticeModal');
            
            // Get buttons
            const showCreateModalBtn = document.getElementById('showCreateModal');
            const editNoticeButtons = document.querySelectorAll('.edit-notice');
            const deleteNoticeButtons = document.querySelectorAll('.delete-notice');
            const closeModalButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            
            // Show create notice modal
            showCreateModalBtn.addEventListener('click', function() {
                createNoticeModal.style.display = 'flex';
            });
            
            // Show edit notice modal
            editNoticeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const noticeId = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const content = this.getAttribute('data-content');
                    const type = this.getAttribute('data-type');
                    const audienceJson = this.getAttribute('data-audience');
                    const startDate = this.getAttribute('data-start');
                    const endDate = this.getAttribute('data-end');
                    const isPublished = this.getAttribute('data-published');
                    
                    // Parse audience JSON
                    let audience = [];
                    try {
                        audience = JSON.parse(audienceJson);
                        if (!Array.isArray(audience)) audience = [];
                    } catch (e) {
                        audience = [];
                    }
                    
                    // Set form values
                    document.getElementById('edit_notice_id').value = noticeId;
                    document.getElementById('edit_title').value = title;
                    document.getElementById('edit_content').value = content;
                    document.getElementById('edit_notice_type').value = type;
                    document.getElementById('edit_start_date').value = startDate;
                    document.getElementById('edit_end_date').value = endDate;
                    document.getElementById('edit_is_published').checked = isPublished == '1';
                    
                    // Reset checkboxes
                    document.getElementById('edit_audience_students').checked = false;
                    document.getElementById('edit_audience_teachers').checked = false;
                    document.getElementById('edit_audience_parents').checked = false;
                    document.getElementById('edit_audience_all').checked = false;
                    
                    // Check appropriate checkboxes
                    audience.forEach(aud => {
                        if (aud === 'students') document.getElementById('edit_audience_students').checked = true;
                        if (aud === 'teachers') document.getElementById('edit_audience_teachers').checked = true;
                        if (aud === 'parents') document.getElementById('edit_audience_parents').checked = true;
                        if (aud === 'all') document.getElementById('edit_audience_all').checked = true;
                    });
                    
                    editNoticeModal.style.display = 'flex';
                });
            });
            
            // Show delete confirmation modal
            deleteNoticeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const noticeId = this.getAttribute('data-id');
                    const noticeTitle = this.getAttribute('data-title');
                    
                    document.getElementById('delete_notice_id').value = noticeId;
                    document.getElementById('deleteNoticeTitle').textContent = noticeTitle;
                    deleteNoticeModal.style.display = 'flex';
                });
            });
            
            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    createNoticeModal.style.display = 'none';
                    editNoticeModal.style.display = 'none';
                    deleteNoticeModal.style.display = 'none';
                });
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === createNoticeModal) {
                    createNoticeModal.style.display = 'none';
                }
                if (event.target === editNoticeModal) {
                    editNoticeModal.style.display = 'none';
                }
                if (event.target === deleteNoticeModal) {
                    deleteNoticeModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>