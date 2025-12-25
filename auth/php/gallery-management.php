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

// Handle gallery item creation (without file upload in this simplified version)
if (isset($_POST['create_gallery_item'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $fileUrl = trim($_POST['file_url']);
    $thumbnailUrl = trim($_POST['thumbnail_url']);
    $category = $_POST['category'];
    $tags = trim($_POST['tags']);
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    
    // Validate input
    if (empty($title) || empty($fileUrl) || empty($category)) {
        $error = "Title, file URL, and category are required.";
    } else {
        try {
            // Convert tags to JSON array
            $tagsArray = array_map('trim', explode(',', $tags));
            $tagsJson = json_encode($tagsArray);
            
            // Create new gallery item
            $stmt = $pdo->prepare("INSERT INTO gallery (title, description, file_url, thumbnail_url, category, tags, is_published, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $fileUrl, $thumbnailUrl, $category, $tagsJson, $isPublished, $_SESSION['user_id']]);
            
            $message = "Gallery item created successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle gallery item updates
if (isset($_POST['update_gallery_item'])) {
    $itemId = (int)$_POST['item_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $fileUrl = trim($_POST['file_url']);
    $thumbnailUrl = trim($_POST['thumbnail_url']);
    $category = $_POST['category'];
    $tags = trim($_POST['tags']);
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    
    // Validate input
    if (empty($title) || empty($fileUrl) || empty($category)) {
        $error = "Title, file URL, and category are required.";
    } else {
        try {
            // Convert tags to JSON array
            $tagsArray = array_map('trim', explode(',', $tags));
            $tagsJson = json_encode($tagsArray);
            
            // Update gallery item
            $stmt = $pdo->prepare("UPDATE gallery SET title = ?, description = ?, file_url = ?, thumbnail_url = ?, category = ?, tags = ?, is_published = ? WHERE id = ?");
            $stmt->execute([$title, $description, $fileUrl, $thumbnailUrl, $category, $tagsJson, $isPublished, $itemId]);
            
            $message = "Gallery item updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle gallery item deletion
if (isset($_POST['delete_gallery_item'])) {
    $itemId = (int)$_POST['item_id'];
    
    try {
        // Delete gallery item
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->execute([$itemId]);
        
        $message = "Gallery item deleted successfully.";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch all gallery items for display
try {
    $stmt = $pdo->query("SELECT id, title, description, file_url, category, tags, is_published, upload_date, created_at FROM gallery ORDER BY created_at DESC");
    $galleryItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $galleryItems = [];
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
    <title>Gallery Management - Excellence School</title>
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
        
        .gallery-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .gallery-table th,
        .gallery-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .gallery-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }
        
        .gallery-table tr:last-child td {
            border-bottom: none;
        }
        
        .gallery-table tr:hover {
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
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
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
        
        .thumbnail-preview {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
        }
        
        @media (max-width: 768px) {
            .editor-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .gallery-table {
                font-size: 0.9rem;
            }
            
            .gallery-table th,
            .gallery-table td {
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
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        
    </header>

    <section class="admin-header">
        <div class="container">
            <h1><i class="fas fa-images"></i> Gallery Management</h1>
            <p>Upload and manage photo gallery</p>
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
                <h1>Gallery Items</h1>
                <button id="showCreateModal" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Item
                </button>
            </div>
            
            <table class="gallery-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Tags</th>
                        <th>Status</th>
                        <th>Upload Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($galleryItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td><?php echo htmlspecialchars(substr($item['title'], 0, 30)) . (strlen($item['title']) > 30 ? '...' : ''); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($item['category'])); ?></td>
                        <td>
                            <?php if (!empty($item['file_url'])): ?>
                                <img src="<?php echo htmlspecialchars($item['file_url']); ?>" alt="Thumbnail" class="thumbnail-preview" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2220%22 fill=%22%23999%22%3EImage%3C/text%3E%3C/svg%3E'">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $tags = json_decode($item['tags'], true);
                            if (is_array($tags)) {
                                echo implode(', ', array_slice($tags, 0, 3));
                                if (count($tags) > 3) echo '...';
                            } else {
                                echo 'None';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($item['is_published']): ?>
                                <span class="status-badge status-published">Published</span>
                            <?php else: ?>
                                <span class="status-badge status-draft">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($item['upload_date'] ?? $item['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-outline btn-small edit-gallery-item" 
                                        data-id="<?php echo $item['id']; ?>"
                                        data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                        data-description="<?php echo htmlspecialchars($item['description'] ?? ''); ?>"
                                        data-file-url="<?php echo htmlspecialchars($item['file_url'] ?? ''); ?>"
                                        data-thumbnail-url="<?php echo htmlspecialchars($item['thumbnail_url'] ?? ''); ?>"
                                        data-category="<?php echo htmlspecialchars($item['category']); ?>"
                                        data-tags="<?php echo htmlspecialchars($item['tags'] ?? '[]'); ?>"
                                        data-published="<?php echo $item['is_published']; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline btn-small delete-gallery-item" 
                                        data-id="<?php echo $item['id']; ?>"
                                        data-title="<?php echo htmlspecialchars($item['title']); ?>">
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

    <!-- Create Gallery Item Modal -->
    <div id="createGalleryItemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-images"></i> Add New Gallery Item</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="createGalleryItemForm" action="" method="POST">
                <div class="form-group">
                    <label for="create_title">Title *</label>
                    <input type="text" id="create_title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="create_description">Description</label>
                    <textarea id="create_description" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_file_url">Image URL *</label>
                            <input type="url" id="create_file_url" name="file_url" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_thumbnail_url">Thumbnail URL</label>
                            <input type="url" id="create_thumbnail_url" name="thumbnail_url" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_category">Category *</label>
                            <select id="create_category" name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="events">Events</option>
                                <option value="activities">Activities</option>
                                <option value="achievements">Achievements</option>
                                <option value="facilities">Facilities</option>
                                <option value="staff">Staff</option>
                                <option value="students">Students</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_tags">Tags (comma separated)</label>
                            <input type="text" id="create_tags" name="tags" class="form-control" placeholder="tag1, tag2, tag3">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="create_is_published" name="is_published" value="1" checked>
                        <label for="create_is_published">Publish Item</label>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="create_gallery_item" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Gallery Item Modal -->
    <div id="editGalleryItemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Gallery Item</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="editGalleryItemForm" action="" method="POST">
                <input type="hidden" id="edit_item_id" name="item_id">
                <div class="form-group">
                    <label for="edit_title">Title *</label>
                    <input type="text" id="edit_title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_file_url">Image URL *</label>
                            <input type="url" id="edit_file_url" name="file_url" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_thumbnail_url">Thumbnail URL</label>
                            <input type="url" id="edit_thumbnail_url" name="thumbnail_url" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_category">Category *</label>
                            <select id="edit_category" name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="events">Events</option>
                                <option value="activities">Activities</option>
                                <option value="achievements">Achievements</option>
                                <option value="facilities">Facilities</option>
                                <option value="staff">Staff</option>
                                <option value="students">Students</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_tags">Tags (comma separated)</label>
                            <input type="text" id="edit_tags" name="tags" class="form-control" placeholder="tag1, tag2, tag3">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="edit_is_published" name="is_published" value="1">
                        <label for="edit_is_published">Publish Item</label>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="update_gallery_item" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteGalleryItemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete gallery item <strong id="deleteGalleryItemTitle"></strong>? This action cannot be undone.</p>
            </div>
            <form id="deleteGalleryItemForm" action="" method="POST">
                <input type="hidden" id="delete_item_id" name="item_id">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="delete_gallery_item" class="btn btn-primary" style="background-color: #dc3545;">
                        <i class="fas fa-trash"></i> Delete Item
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
            const createGalleryItemModal = document.getElementById('createGalleryItemModal');
            const editGalleryItemModal = document.getElementById('editGalleryItemModal');
            const deleteGalleryItemModal = document.getElementById('deleteGalleryItemModal');
            
            // Get buttons
            const showCreateModalBtn = document.getElementById('showCreateModal');
            const editGalleryItemButtons = document.querySelectorAll('.edit-gallery-item');
            const deleteGalleryItemButtons = document.querySelectorAll('.delete-gallery-item');
            const closeModalButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            
            // Show create gallery item modal
            showCreateModalBtn.addEventListener('click', function() {
                createGalleryItemModal.style.display = 'flex';
            });
            
            // Show edit gallery item modal
            editGalleryItemButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const description = this.getAttribute('data-description');
                    const fileUrl = this.getAttribute('data-file-url');
                    const thumbnailUrl = this.getAttribute('data-thumbnail-url');
                    const category = this.getAttribute('data-category');
                    const tagsJson = this.getAttribute('data-tags');
                    const isPublished = this.getAttribute('data-published');
                    
                    // Parse tags JSON
                    let tagsString = '';
                    try {
                        const tags = JSON.parse(tagsJson);
                        if (Array.isArray(tags)) {
                            tagsString = tags.join(', ');
                        }
                    } catch (e) {
                        tagsString = '';
                    }
                    
                    // Set form values
                    document.getElementById('edit_item_id').value = itemId;
                    document.getElementById('edit_title').value = title;
                    document.getElementById('edit_description').value = description;
                    document.getElementById('edit_file_url').value = fileUrl;
                    document.getElementById('edit_thumbnail_url').value = thumbnailUrl;
                    document.getElementById('edit_category').value = category;
                    document.getElementById('edit_tags').value = tagsString;
                    document.getElementById('edit_is_published').checked = isPublished == '1';
                    
                    editGalleryItemModal.style.display = 'flex';
                });
            });
            
            // Show delete confirmation modal
            deleteGalleryItemButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-id');
                    const itemTitle = this.getAttribute('data-title');
                    
                    document.getElementById('delete_item_id').value = itemId;
                    document.getElementById('deleteGalleryItemTitle').textContent = itemTitle;
                    deleteGalleryItemModal.style.display = 'flex';
                });
            });
            
            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    createGalleryItemModal.style.display = 'none';
                    editGalleryItemModal.style.display = 'none';
                    deleteGalleryItemModal.style.display = 'none';
                });
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === createGalleryItemModal) {
                    createGalleryItemModal.style.display = 'none';
                }
                if (event.target === editGalleryItemModal) {
                    editGalleryItemModal.style.display = 'none';
                }
                if (event.target === deleteGalleryItemModal) {
                    deleteGalleryItemModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>