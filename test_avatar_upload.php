<?php
// Test avatar upload functionality
echo "<h1>Avatar Upload Test</h1>";

// Check if uploads directory exists
if (is_dir('../../uploads/avatars')) {
    echo "<p>✅ Uploads directory exists</p>";
} else {
    echo "<p>❌ Uploads directory does not exist</p>";
}

// Check if we can write to the directory
if (is_writable('../../uploads/avatars')) {
    echo "<p>✅ Uploads directory is writable</p>";
} else {
    echo "<p>❌ Uploads directory is not writable</p>";
}

// Test file creation
$testFile = '../../uploads/avatars/test.txt';
if (file_put_contents($testFile, 'Test file for avatar upload functionality')) {
    echo "<p>✅ Can write files to uploads directory</p>";
    unlink($testFile); // Clean up test file
} else {
    echo "<p>❌ Cannot write files to uploads directory</p>";
}

echo "<p><a href='auth/php/parent-profile.php'>Go to Parent Profile</a></p>";
?>