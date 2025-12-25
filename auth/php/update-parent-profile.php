<?php
session_start();
include_once '../../config/dbconnection.php';

// Check if user is logged in and is a parent
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'parent') {
    header('Location: parent-login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle avatar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/avatars/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['avatar']['tmp_name']);
        
        if (in_array($fileType, $allowedTypes)) {
            // Generate unique filename
            $fileExtension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $newFilename = uniqid() . '_' . $user_id . '.' . $fileExtension;
            $uploadPath = $uploadDir . $newFilename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
                try {
                    // Check if user profile exists
                    $stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($profile) {
                        // Update existing profile
                        $stmt = $pdo->prepare("UPDATE user_profiles SET avatar = ? WHERE user_id = ?");
                        $stmt->execute([$newFilename, $user_id]);
                    } else {
                        // Create new profile
                        $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, avatar) VALUES (?, ?)");
                        $stmt->execute([$user_id, $newFilename]);
                    }
                    
                    $avatarSuccess = "Avatar uploaded successfully!";
                } catch (PDOException $e) {
                    $avatarError = "Database error: " . $e->getMessage();
                }
            } else {
                $avatarError = "Failed to move uploaded file.";
            }
        } else {
            $avatarError = "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
        }
    } else {
        $avatarError = "Please select a file to upload.";
    }
}

// Prepare redirect URL with success/error messages
$redirectUrl = 'parent-profile.php';
if (isset($avatarSuccess)) {
    $redirectUrl .= '?avatar_success=1';
} elseif (isset($avatarError)) {
    $redirectUrl .= '?avatar_error=' . urlencode($avatarError);
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $dateOfBirth = $_POST['dateOfBirth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    $occupation = $_POST['occupation'] ?? '';
    $relationship = $_POST['relationship'] ?? '';
    
    // Combine first and last name
    $fullName = trim($firstName . ' ' . $lastName);
    
    try {
        // Update users table
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        $stmt->execute([$fullName, $email, $user_id]);
        
        // Check if user profile exists
        $stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($profile) {
            // Update existing profile
            $stmt = $pdo->prepare("UPDATE user_profiles SET phone = ?, date_of_birth = ?, gender = ?, address = ?, occupation = ?, relationship = ? WHERE user_id = ?");
            $stmt->execute([$phone, $dateOfBirth, $gender, $address, $occupation, $relationship, $user_id]);
        } else {
            // Create new profile
            $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, phone, date_of_birth, gender, address, occupation, relationship) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $phone, $dateOfBirth, $gender, $address, $occupation, $relationship]);
        }
        
        $profileSuccess = "Profile updated successfully!";
        
        // Update session variables if needed
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_email'] = $email;
    } catch (PDOException $e) {
        $profileError = "Database error: " . $e->getMessage();
    }
}

// Add profile success/error to redirect URL
if (isset($profileSuccess)) {
    $redirectUrl .= (strpos($redirectUrl, '?') !== false ? '&' : '?') . 'profile_success=1';
} elseif (isset($profileError)) {
    $redirectUrl .= (strpos($redirectUrl, '?') !== false ? '&' : '?') . 'profile_error=' . urlencode($profileError);
}

// Redirect back to parent profile page
header('Location: ' . $redirectUrl);
exit();
?>