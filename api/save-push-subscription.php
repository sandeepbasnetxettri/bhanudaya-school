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

if (!$data || !isset($data['endpoint']) || !isset($data['p256dh']) || !isset($data['auth'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required subscription data']);
    exit;
}

try {
    // Check if subscription already exists
    $stmt = $pdo->prepare("SELECT id FROM push_subscriptions WHERE endpoint = ?");
    $stmt->execute([$data['endpoint']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Update existing subscription
        $stmt = $pdo->prepare("UPDATE push_subscriptions SET user_id = ?, p256dh = ?, auth = ?, updated_at = CURRENT_TIMESTAMP WHERE endpoint = ?");
        $stmt->execute([$_SESSION['user_id'], $data['p256dh'], $data['auth'], $data['endpoint']]);
    } else {
        // Insert new subscription
        $stmt = $pdo->prepare("INSERT INTO push_subscriptions (user_id, endpoint, p256dh, auth) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $data['endpoint'], $data['p256dh'], $data['auth']]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Subscription saved successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>