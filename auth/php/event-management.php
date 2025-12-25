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

// Handle event creation
if (isset($_POST['create_event'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $eventType = $_POST['event_type'];
    $startDatetime = $_POST['start_datetime'];
    $endDatetime = $_POST['end_datetime'];
    $location = trim($_POST['location']);
    $organizer = trim($_POST['organizer']);
    $isAllDay = isset($_POST['is_all_day']) ? 1 : 0;
    $isRecurring = isset($_POST['is_recurring']) ? 1 : 0;
    $recurrencePattern = $_POST['recurrence_pattern'];
    
    // Validate input
    if (empty($title) || empty($eventType) || empty($startDatetime) || empty($endDatetime)) {
        $error = "Title, event type, start date, and end date are required.";
    } else {
        try {
            // Create new event
            $stmt = $pdo->prepare("INSERT INTO events (title, description, event_type, start_datetime, end_datetime, location, organizer, is_all_day, is_recurring, recurrence_pattern, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $eventType, $startDatetime, $endDatetime, $location, $organizer, $isAllDay, $isRecurring, $recurrencePattern, $_SESSION['user_id']]);
            
            $message = "Event created successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle event updates
if (isset($_POST['update_event'])) {
    $eventId = (int)$_POST['event_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $eventType = $_POST['event_type'];
    $startDatetime = $_POST['start_datetime'];
    $endDatetime = $_POST['end_datetime'];
    $location = trim($_POST['location']);
    $organizer = trim($_POST['organizer']);
    $isAllDay = isset($_POST['is_all_day']) ? 1 : 0;
    $isRecurring = isset($_POST['is_recurring']) ? 1 : 0;
    $recurrencePattern = $_POST['recurrence_pattern'];
    
    // Validate input
    if (empty($title) || empty($eventType) || empty($startDatetime) || empty($endDatetime)) {
        $error = "Title, event type, start date, and end date are required.";
    } else {
        try {
            // Update event
            $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, event_type = ?, start_datetime = ?, end_datetime = ?, location = ?, organizer = ?, is_all_day = ?, is_recurring = ?, recurrence_pattern = ? WHERE id = ?");
            $stmt->execute([$title, $description, $eventType, $startDatetime, $endDatetime, $location, $organizer, $isAllDay, $isRecurring, $recurrencePattern, $eventId]);
            
            $message = "Event updated successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle event deletion
if (isset($_POST['delete_event'])) {
    $eventId = (int)$_POST['event_id'];
    
    try {
        // Delete event
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        
        $message = "Event deleted successfully.";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch all events for display
try {
    $stmt = $pdo->query("SELECT id, title, event_type, start_datetime, end_datetime, location, organizer, is_all_day, is_recurring, created_at FROM events ORDER BY start_datetime DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $events = [];
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
    <title>Events Management - Excellence School</title>
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
        
        .event-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .event-table th,
        .event-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .event-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }
        
        .event-table tr:last-child td {
            border-bottom: none;
        }
        
        .event-table tr:hover {
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
        
        @media (max-width: 768px) {
            .editor-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .event-table {
                font-size: 0.9rem;
            }
            
            .event-table th,
            .event-table td {
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
            <h1><i class="fas fa-calendar-day"></i> Events Management</h1>
            <p>Schedule and manage school events</p>
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
                <h1>Event Records</h1>
                <button id="showCreateModal" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Event
                </button>
            </div>
            
            <table class="event-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Location</th>
                        <th>Organizer</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['id']); ?></td>
                        <td><?php echo htmlspecialchars(substr($event['title'], 0, 30)) . (strlen($event['title']) > 30 ? '...' : ''); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($event['event_type'])); ?></td>
                        <td><?php echo date('M j, Y g:i A', strtotime($event['start_datetime'])); ?></td>
                        <td><?php echo date('M j, Y g:i A', strtotime($event['end_datetime'])); ?></td>
                        <td><?php echo htmlspecialchars($event['location'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($event['organizer'] ?? 'N/A'); ?></td>
                        <td><?php echo date('M j, Y', strtotime($event['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-outline btn-small edit-event" 
                                        data-id="<?php echo $event['id']; ?>"
                                        data-title="<?php echo htmlspecialchars($event['title']); ?>"
                                        data-description="<?php echo htmlspecialchars($event['description'] ?? ''); ?>"
                                        data-type="<?php echo htmlspecialchars($event['event_type']); ?>"
                                        data-start="<?php echo htmlspecialchars($event['start_datetime']); ?>"
                                        data-end="<?php echo htmlspecialchars($event['end_datetime']); ?>"
                                        data-location="<?php echo htmlspecialchars($event['location'] ?? ''); ?>"
                                        data-organizer="<?php echo htmlspecialchars($event['organizer'] ?? ''); ?>"
                                        data-all-day="<?php echo $event['is_all_day']; ?>"
                                        data-recurring="<?php echo $event['is_recurring']; ?>"
                                        data-recurrence="<?php echo htmlspecialchars($event['recurrence_pattern'] ?? ''); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline btn-small delete-event" 
                                        data-id="<?php echo $event['id']; ?>"
                                        data-title="<?php echo htmlspecialchars($event['title']); ?>">
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

    <!-- Create Event Modal -->
    <div id="createEventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-calendar-day"></i> Add New Event</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="createEventForm" action="" method="POST">
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
                            <label for="create_event_type">Event Type *</label>
                            <select id="create_event_type" name="event_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="academic">Academic</option>
                                <option value="cultural">Cultural</option>
                                <option value="sports">Sports</option>
                                <option value="meeting">Meeting</option>
                                <option value="holiday">Holiday</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_location">Location</label>
                            <input type="text" id="create_location" name="location" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_start_datetime">Start Date & Time *</label>
                            <input type="datetime-local" id="create_start_datetime" name="start_datetime" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="create_end_datetime">End Date & Time *</label>
                            <input type="datetime-local" id="create_end_datetime" name="end_datetime" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="create_organizer">Organizer</label>
                    <input type="text" id="create_organizer" name="organizer" class="form-control">
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="create_is_all_day" name="is_all_day" value="1">
                                <label for="create_is_all_day">All Day Event</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="create_is_recurring" name="is_recurring" value="1">
                                <label for="create_is_recurring">Recurring Event</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="create_recurrence_pattern">Recurrence Pattern</label>
                    <select id="create_recurrence_pattern" name="recurrence_pattern" class="form-control">
                        <option value="">Select Pattern</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="create_event" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Event
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div id="editEventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Event</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="editEventForm" action="" method="POST">
                <input type="hidden" id="edit_event_id" name="event_id">
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
                            <label for="edit_event_type">Event Type *</label>
                            <select id="edit_event_type" name="event_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="academic">Academic</option>
                                <option value="cultural">Cultural</option>
                                <option value="sports">Sports</option>
                                <option value="meeting">Meeting</option>
                                <option value="holiday">Holiday</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_location">Location</label>
                            <input type="text" id="edit_location" name="location" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_start_datetime">Start Date & Time *</label>
                            <input type="datetime-local" id="edit_start_datetime" name="start_datetime" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit_end_datetime">End Date & Time *</label>
                            <input type="datetime-local" id="edit_end_datetime" name="end_datetime" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_organizer">Organizer</label>
                    <input type="text" id="edit_organizer" name="organizer" class="form-control">
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="edit_is_all_day" name="is_all_day" value="1">
                                <label for="edit_is_all_day">All Day Event</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="edit_is_recurring" name="is_recurring" value="1">
                                <label for="edit_is_recurring">Recurring Event</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_recurrence_pattern">Recurrence Pattern</label>
                    <select id="edit_recurrence_pattern" name="recurrence_pattern" class="form-control">
                        <option value="">Select Pattern</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="update_event" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Event
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteEventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete event <strong id="deleteEventTitle"></strong>? This action cannot be undone.</p>
            </div>
            <form id="deleteEventForm" action="" method="POST">
                <input type="hidden" id="delete_event_id" name="event_id">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline close-modal-btn">Cancel</button>
                    <button type="submit" name="delete_event" class="btn btn-primary" style="background-color: #dc3545;">
                        <i class="fas fa-trash"></i> Delete Event
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
            const createEventModal = document.getElementById('createEventModal');
            const editEventModal = document.getElementById('editEventModal');
            const deleteEventModal = document.getElementById('deleteEventModal');
            
            // Get buttons
            const showCreateModalBtn = document.getElementById('showCreateModal');
            const editEventButtons = document.querySelectorAll('.edit-event');
            const deleteEventButtons = document.querySelectorAll('.delete-event');
            const closeModalButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            
            // Show create event modal
            showCreateModalBtn.addEventListener('click', function() {
                createEventModal.style.display = 'flex';
            });
            
            // Show edit event modal
            editEventButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const description = this.getAttribute('data-description');
                    const type = this.getAttribute('data-type');
                    const startDate = this.getAttribute('data-start');
                    const endDate = this.getAttribute('data-end');
                    const location = this.getAttribute('data-location');
                    const organizer = this.getAttribute('data-organizer');
                    const isAllDay = this.getAttribute('data-all-day');
                    const isRecurring = this.getAttribute('data-recurring');
                    const recurrencePattern = this.getAttribute('data-recurrence');
                    
                    // Set form values
                    document.getElementById('edit_event_id').value = eventId;
                    document.getElementById('edit_title').value = title;
                    document.getElementById('edit_description').value = description;
                    document.getElementById('edit_event_type').value = type;
                    document.getElementById('edit_start_datetime').value = startDate;
                    document.getElementById('edit_end_datetime').value = endDate;
                    document.getElementById('edit_location').value = location;
                    document.getElementById('edit_organizer').value = organizer;
                    document.getElementById('edit_is_all_day').checked = isAllDay == '1';
                    document.getElementById('edit_is_recurring').checked = isRecurring == '1';
                    document.getElementById('edit_recurrence_pattern').value = recurrencePattern;
                    
                    editEventModal.style.display = 'flex';
                });
            });
            
            // Show delete confirmation modal
            deleteEventButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-id');
                    const eventTitle = this.getAttribute('data-title');
                    
                    document.getElementById('delete_event_id').value = eventId;
                    document.getElementById('deleteEventTitle').textContent = eventTitle;
                    deleteEventModal.style.display = 'flex';
                });
            });
            
            // Close modals
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    createEventModal.style.display = 'none';
                    editEventModal.style.display = 'none';
                    deleteEventModal.style.display = 'none';
                });
            });
            
            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === createEventModal) {
                    createEventModal.style.display = 'none';
                }
                if (event.target === editEventModal) {
                    editEventModal.style.display = 'none';
                }
                if (event.target === deleteEventModal) {
                    deleteEventModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>