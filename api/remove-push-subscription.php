<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}

// Database connection
require_once '../config/dbconnection.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['endpoint'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing endpoint data']);
    exit;
}

try {
    // Remove subscription
    $stmt = $pdo->prepare("DELETE FROM push_subscriptions WHERE endpoint = ? AND user_id = ?");
    $stmt->execute([$data['endpoint'], $_SESSION['user_id']]);
    
    echo json_encode(['success' => true, 'message' => 'Subscription removed successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>