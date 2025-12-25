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
require_once 'notify-new-notice.php';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (empty($data['title']) || empty($data['content']) || empty($data['notice_type'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Title, content, and notice type are required']);
        exit;
    }
    
    $title = trim($data['title']);
    $content = trim($data['content']);
    $noticeType = $data['notice_type'];
    $targetAudience = $data['target_audience'] ?? '';
    $startDate = $data['start_date'] ?? null;
    $endDate = $data['end_date'] ?? null;
    $isPublished = isset($data['is_published']) ? 1 : 0;
    $postedBy = $_SESSION['user_id'];
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Insert notice
        $stmt = $pdo->prepare("
            INSERT INTO notices 
            (title, content, notice_type, posted_by, target_audience, start_date, end_date, is_published) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $title, 
            $content, 
            $noticeType, 
            $postedBy, 
            $targetAudience, 
            $startDate, 
            $endDate, 
            $isPublished
        ]);
        
        $noticeId = $pdo->lastInsertId();
        
        // Commit transaction
        $pdo->commit();
        
        // Send push notifications if the notice is published
        if ($isPublished) {
            notifyNewNotice($noticeId, $title, $content, $postedBy);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Notice created successfully',
            'notice_id' => $noticeId
        ]);
    } catch (PDOException $e) {
        // Rollback transaction
        $pdo->rollback();
        
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        // Rollback transaction
        $pdo->rollback();
        
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>