<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied. Admin privileges required.']);
    exit;
}

// Database connection
require_once '../config/dbconnection.php';
require_once 'send-push-notification.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (empty($data['title']) || empty($data['body'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Title and body are required']);
        exit;
    }
    
    $title = trim($data['title']);
    $body = trim($data['body']);
    $recipient = $data['recipient'] ?? 'all'; // 'all' or specific user ID
    $url = $data['url'] ?? '/';
    
    // Prepare notification payload
    $payload = [
        'title' => $title,
        'body' => $body,
        'icon' => '/images/school-icon.png',
        'url' => $url,
        'timestamp' => date('c')
    ];
    
    try {
        $success = false;
        
        if ($recipient === 'all') {
            // Check if there are any subscriptions first
            if (!hasPushSubscriptions()) {
                echo json_encode(['success' => false, 'error' => 'Failed to send notification - no subscribers found']);
                exit;
            }
            // Send to all users
            $success = sendPushNotificationToAllUsers($payload);
        } elseif (is_numeric($recipient)) {
            // Send to specific user
            $success = sendPushNotificationToUser((int)$recipient, $payload);
        } elseif (in_array($recipient, ['students', 'teachers', 'parents'])) {
            // Send to users with specific role
            // Map UI values to database roles
            $roleMap = [
                'students' => 'student',
                'teachers' => 'teacher',
                'parents' => 'parent'
            ];
            $role = $roleMap[$recipient];
            // Check if there are subscriptions for this role
            if (!hasPushSubscriptionsForRole($role)) {
                echo json_encode(['success' => false, 'error' => 'Failed to send notification - no subscribers found for ' . $recipient]);
                exit;
            }
            $success = sendPushNotificationToRole($role, $payload);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid recipient specified']);
            exit;
        }
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Notification sent successfully']);
        } else {
            // Check if it's because no subscriptions were found
            if ($recipient === 'all') {
                echo json_encode(['success' => false, 'error' => 'Failed to send notification - no subscribers found']);
            } elseif (in_array($recipient, ['students', 'teachers', 'parents'])) {
                echo json_encode(['success' => false, 'error' => 'Failed to send notification - no subscribers found for ' . $recipient]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to send notification']);
            }
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    }
} else {
    // Return form for testing
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Send Test Notification</title>
        <link rel="stylesheet" href="../css/style.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 600px;
                margin: 50px auto;
                padding: 20px;
                background-color: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #4CAF50;
                text-align: center;
            }
            .form-group {
                margin-bottom: 20px;
            }
            label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }
            input, textarea, select {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                box-sizing: border-box;
            }
            button {
                background-color: #4CAF50;
                color: white;
                padding: 12px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                width: 100%;
            }
            button:hover {
                background-color: #45a049;
            }
            .result {
                margin-top: 20px;
                padding: 15px;
                border-radius: 5px;
            }
            .success {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }
            .error {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Send Test Push Notification</h1>
            
            <form id="notificationForm">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="body">Body:</label>
                    <textarea id="body" name="body" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="recipient">Recipient:</label>
                    <select id="recipient" name="recipient">
                        <option value="all">All Users</option>
                        <option value="1">User ID 1 (Test)</option>
                        <option value="2">User ID 2 (Test)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="url">URL (optional):</label>
                    <input type="text" id="url" name="url" placeholder="/" value="/">
                </div>
                
                <button type="submit">Send Notification</button>
            </form>
            
            <div id="result"></div>
        </div>
        
        <script>
            document.getElementById('notificationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    title: document.getElementById('title').value,
                    body: document.getElementById('body').value,
                    recipient: document.getElementById('recipient').value,
                    url: document.getElementById('url').value
                };
                
                fetch('send-test-notification.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    const resultDiv = document.getElementById('result');
                    if (data.success) {
                        resultDiv.innerHTML = '<div class="result success">Success: ' + data.message + '</div>';
                        // Reset form
                        document.getElementById('notificationForm').reset();
                    } else {
                        resultDiv.innerHTML = '<div class="result error">Error: ' + data.error + '</div>';
                    }
                })
                .catch(error => {
                    document.getElementById('result').innerHTML = '<div class="result error">Error: ' + error.message + '</div>';
                });
            });
        </script>
    </body>
    </html>
    <?php
}
?>