<?php
// Diagnostic script to check parent portal pages

echo "<h1>Parent Portal Pages Diagnostic</h1>";

// Check if files exist
$files = [
    'auth/php/parent-login.php',
    'auth/php/parent-register.php',
    'auth/php/parent-dashboard.php',
    'auth/php/parent-profile.php'
];

echo "<h2>File Existence Check</h2>";
foreach ($files as $file) {
    $exists = file_exists($file) ? "✓ Exists" : "✗ Missing";
    echo "<p><strong>$file:</strong> $exists</p>";
}

// Check database connection
echo "<h2>Database Connection Check</h2>";
try {
    include_once 'config/dbconnection.php';
    echo "<p>✓ Database connection successful</p>";
    
    // Check if required tables exist
    $tables = ['users', 'students'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $result = $stmt->fetch();
        $exists = $result ? "✓ Exists" : "✗ Missing";
        echo "<p><strong>Table '$table':</strong> $exists</p>";
    }
} catch (Exception $e) {
    echo "<p>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Session Test</h2>";
session_start();
$_SESSION['test'] = 'working';
echo "<p>Session status: " . (isset($_SESSION['test']) ? "✓ Working" : "✗ Not working") . "</p>";

echo "<h2>Directory Structure</h2>";
echo "<pre>";
print_r(scandir('.'));
echo "</pre>";

echo "<h2>Auth Directory Structure</h2>";
if (is_dir('auth/php')) {
    echo "<pre>";
    print_r(scandir('auth/php'));
    echo "</pre>";
} else {
    echo "<p>Auth directory not found</p>";
}
?>