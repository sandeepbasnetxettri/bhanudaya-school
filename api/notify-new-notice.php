<?php
/**
 * Script to send push notifications when a new notice is posted
 * This script should be called whenever a new notice is created in the system
 */

require_once '../config/dbconnection.php';
require_once 'send-push-notification.php';

/**
 * Notify users about a new notice
 * 
 * @param int $noticeId The ID of the newly created notice
 * @param string $title The notice title
 * @param string $content The notice content
 * @param int $postedBy User ID of the person who posted the notice
 * @return bool Whether notifications were sent successfully
 */
function notifyNewNotice($noticeId, $title, $content, $postedBy) {
    global $pdo;
    
    try {
        // Prepare notification payload
        $payload = [
            'title' => 'New Notice: ' . $title,
            'body' => substr($content, 0, 100) . (strlen($content) > 100 ? '...' : ''),
            'icon' => '/images/school-icon.png',
            'url' => '/pages/notice.php?id=' . $noticeId,
            'timestamp' => date('c'),
            'notice_id' => $noticeId
        ];
        
        // Send to all users who have push notifications enabled
        // First, get users with push notifications enabled
        $stmt = $pdo->prepare("
            SELECT up.user_id 
            FROM user_profiles up 
            JOIN users u ON up.user_id = u.id 
            WHERE up.push_notifications = 1 
            AND u.id != ?
        ");
        $stmt->execute([$postedBy]);
        $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $successCount = 0;
        
        // Send notification to each user
        foreach ($users as $userId) {
            if (sendPushNotificationToUser($userId, $payload)) {
                $successCount++;
            }
        }
        
        // Log the notification sending
        error_log("Sent push notifications for notice #{$noticeId} to {$successCount} users");
        
        return $successCount > 0;
    } catch (PDOException $e) {
        error_log("Database error in notifyNewNotice: " . $e->getMessage());
        return false;
    } catch (Exception $e) {
        error_log("Error in notifyNewNotice: " . $e->getMessage());
        return false;
    }
}

/**
 * Notify specific users about a notice
 * 
 * @param int $noticeId The ID of the notice
 * @param string $title The notice title
 * @param string $content The notice content
 * @param array $userIds Array of user IDs to notify
 * @return bool Whether notifications were sent successfully
 */
function notifySpecificUsers($noticeId, $title, $content, $userIds) {
    try {
        // Prepare notification payload
        $payload = [
            'title' => 'Important Notice: ' . $title,
            'body' => substr($content, 0, 100) . (strlen($content) > 100 ? '...' : ''),
            'icon' => '/images/school-icon.png',
            'url' => '/pages/notice.php?id=' . $noticeId,
            'timestamp' => date('c'),
            'notice_id' => $noticeId
        ];
        
        $successCount = 0;
        
        // Send notification to each user
        foreach ($userIds as $userId) {
            if (sendPushNotificationToUser($userId, $payload)) {
                $successCount++;
            }
        }
        
        return $successCount > 0;
    } catch (Exception $e) {
        error_log("Error in notifySpecificUsers: " . $e->getMessage());
        return false;
    }
}

// Example usage (uncomment to test):
/*
notifyNewNotice(
    123, 
    'School Closure', 
    'The school will be closed tomorrow due to maintenance. All classes are suspended.', 
    1
);
*/

?>