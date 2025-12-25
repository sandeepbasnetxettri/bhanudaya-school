<?php
// This script sends push notifications to subscribed users
require_once '../config/dbconnection.php';

/**
 * Check if there are any push subscriptions in the database
 * 
 * @return bool Whether there are any subscriptions
 */
function hasPushSubscriptions() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM push_subscriptions");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
    } catch (PDOException $e) {
        error_log("Database error in hasPushSubscriptions: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if there are push subscriptions for users with a specific role
 * 
 * @param string $role The user role ('student', 'teacher', 'parent')
 * @return bool Whether there are subscriptions for users with this role
 */
function hasPushSubscriptionsForRole($role) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM push_subscriptions ps JOIN users u ON ps.user_id = u.id WHERE u.role = ?");
        $stmt->execute([$role]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    } catch (PDOException $e) {
        error_log("Database error in hasPushSubscriptionsForRole: " . $e->getMessage());
        return false;
    }
}

/**
 * Send a push notification to a specific user
 * 
 * @param int $userId The ID of the user to send notification to
 * @param array $payload The notification payload (title, body, etc.)
 * @return bool Whether the notification was sent successfully
 */
function sendPushNotificationToUser($userId, $payload) {
    global $pdo;
    
    try {
        // Get user's push subscriptions
        $stmt = $pdo->prepare("SELECT endpoint, p256dh, auth FROM push_subscriptions WHERE user_id = ?");
        $stmt->execute([$userId]);
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($subscriptions)) {
            // No subscriptions found for this user
            return false;
        }
        
        $successCount = 0;
        
        // Send notification to each subscription
        foreach ($subscriptions as $subscription) {
            if (sendWebPush($subscription, $payload)) {
                $successCount++;
            }
        }
        
        return $successCount > 0;
    } catch (PDOException $e) {
        error_log("Database error in sendPushNotificationToUser: " . $e->getMessage());
        return false;
    }
}

/**
 * Send a push notification to all users
 * 
 * @param array $payload The notification payload (title, body, etc.)
 * @return bool Whether any notifications were sent successfully
 */
function sendPushNotificationToAllUsers($payload) {
    global $pdo;
    
    try {
        // Get all push subscriptions
        $stmt = $pdo->prepare("SELECT user_id, endpoint, p256dh, auth FROM push_subscriptions");
        $stmt->execute();
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($subscriptions)) {
            // No subscriptions found
            return false;
        }
        
        $successCount = 0;
        
        // Send notification to each subscription
        foreach ($subscriptions as $subscription) {
            if (sendWebPush($subscription, $payload)) {
                $successCount++;
            }
        }
        
        return $successCount > 0;
    } catch (PDOException $e) {
        error_log("Database error in sendPushNotificationToAllUsers: " . $e->getMessage());
        return false;
    }
}

/**
 * Send a web push notification using cURL
 * 
 * @param array $subscription Subscription data (endpoint, p256dh, auth)
 * @param array $payload The notification payload
 * @return bool Whether the notification was sent successfully
 */
function sendWebPush($subscription, $payload) {
    // Convert payload to JSON
    $payloadJson = json_encode($payload);
    
    // Log the attempt
    error_log("Attempting to send push notification to endpoint: " . $subscription['endpoint']);
    
    // Create HTTP headers
    $headers = [
        'Content-Type: application/octet-stream',
        'Content-Length: ' . strlen($payloadJson),
        'Authorization: WebPush ', // In a real implementation, you would add VAPID auth here
        'TTL: 60'
    ];
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt_array($ch, [
        CURLOPT_URL => $subscription['endpoint'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payloadJson,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT => 30
    ]);
    
    // Execute cURL request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    // Close cURL
    curl_close($ch);
    
    // Check for cURL errors
    if ($error) {
        error_log("cURL error when sending push notification: " . $error);
        return false;
    }
    
    // Check HTTP response code
    if ($httpCode >= 200 && $httpCode < 300) {
        error_log("Push notification sent successfully. HTTP code: " . $httpCode);
        return true;
    } else {
        error_log("HTTP error when sending push notification. HTTP code: " . $httpCode . " - Response: " . $response);
        return false;
    }
}

/**
 * Example function to send a notification when a new notice is posted
 * 
 * @param string $title The notice title
 * @param string $content The notice content
 * @param int $postedBy User ID of the person who posted the notice
 */
function notifyNewNotice($title, $content, $postedBy) {
    // In a real implementation, you would determine which users should receive this notice
    // For now, we'll send to all users
    
    $payload = [
        'title' => 'New Notice: ' . $title,
        'body' => substr($content, 0, 100) . (strlen($content) > 100 ? '...' : ''),
        'icon' => '/images/school-icon.png',
        'url' => '/pages/notice.php',
        'timestamp' => date('c')
    ];
    
    return sendPushNotificationToAllUsers($payload);
}

/**
 * Example function to send a notification when grades are published
 * 
 * @param int $studentId The student ID
 * @param string $subject The subject name
 * @param string $grade The grade received
 */
function notifyGradePublished($studentId, $subject, $grade) {
    $payload = [
        'title' => 'Grade Published',
        'body' => "Your grade for {$subject} is now available: {$grade}",
        'icon' => '/images/grades-icon.png',
        'url' => '/auth/php/student-portal.php#grades',
        'timestamp' => date('c')
    ];
    
    return sendPushNotificationToUser($studentId, $payload);
}

/**
 * Send a push notification to users with a specific role
 * 
 * @param string $role The user role ('student', 'teacher', 'parent')
 * @param array $payload The notification payload (title, body, etc.)
 * @return bool Whether any notifications were sent successfully
 */
function sendPushNotificationToRole($role, $payload) {
    global $pdo;
    
    try {
        // Get push subscriptions for users with the specified role
        $stmt = $pdo->prepare("SELECT ps.user_id, ps.endpoint, ps.p256dh, ps.auth FROM push_subscriptions ps JOIN users u ON ps.user_id = u.id WHERE u.role = ?");
        $stmt->execute([$role]);
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($subscriptions)) {
            // No subscriptions found for users with this role
            error_log("No push subscriptions found for users with role: " . $role);
            return false;
        }
        
        error_log("Found " . count($subscriptions) . " subscriptions for role: " . $role);
        
        $successCount = 0;
        $failureCount = 0;
        
        // Send notification to each subscription
        foreach ($subscriptions as $subscription) {
            if (sendWebPush($subscription, $payload)) {
                $successCount++;
            } else {
                $failureCount++;
                error_log("Failed to send push notification to user ID: " . $subscription['user_id'] . " with endpoint: " . $subscription['endpoint']);
            }
        }
        
        error_log("Push notifications sent - Success: " . $successCount . ", Failures: " . $failureCount . " for role: " . $role);
        
        // Return true if at least one notification was sent successfully
        // Return false if no notifications were sent successfully
        return $successCount > 0;
    } catch (PDOException $e) {
        error_log("Database error in sendPushNotificationToRole: " . $e->getMessage());
        return false;
    }
}

// Example usage (uncomment to test):
// notifyNewNotice('School Closure', 'The school will be closed tomorrow due to maintenance.', 1);
// notifyGradePublished(1, 'Mathematics', 'A+');
?>