<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Check - Push Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4CAF50;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .success { color: green; }
        .warning { color: orange; }
        .error { color: red; }
        ul {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        li {
            margin-bottom: 5px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Database Check for Push Notifications</h2>
        
        <?php
        require_once 'config/dbconnection.php';

        try {
            // Check if push_subscriptions table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'push_subscriptions'");
            $tableExists = $stmt->fetch();
            
            if ($tableExists) {
                echo "<p class='success'>✓ Push subscriptions table exists</p>\n";
                
                // Count total subscriptions
                $stmt = $pdo->query("SELECT COUNT(*) FROM push_subscriptions");
                $count = $stmt->fetchColumn();
                echo "<p>Total push subscriptions: <strong>" . $count . "</strong></p>\n";
                
                if ($count > 0) {
                    // Show subscriptions by role
                    $stmt = $pdo->query("SELECT u.role, COUNT(*) as count FROM push_subscriptions ps JOIN users u ON ps.user_id = u.id GROUP BY u.role");
                    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo "<p>Subscriptions by role:</p>\n<ul>\n";
                    foreach ($roles as $role) {
                        echo "<li><strong>" . htmlspecialchars($role['role']) . ":</strong> " . $role['count'] . "</li>\n";
                    }
                    echo "</ul>\n";
                    
                    echo "<p class='success'>✓ Push notifications can be sent to subscribers</p>\n";
                } else {
                    echo "<p class='warning'>⚠ No push subscriptions found in the database</p>\n";
                    echo "<p>This is the most likely cause of the 'Failed to send notification' error.</p>\n";
                    echo "<p><strong>Solution:</strong> Users need to subscribe to push notifications through the website frontend.</p>\n";
                }
            } else {
                echo "<p class='error'>✗ Push subscriptions table does not exist</p>\n";
                echo "<p>You may need to run the database schema updates.</p>\n";
                echo "<p><strong>Solution:</strong> Run the push_notifications_schema.sql script to create the required tables.</p>\n";
            }
            
            // Check users table
            $stmt = $pdo->query("SELECT COUNT(*) FROM users");
            $userCount = $stmt->fetchColumn();
            echo "<p>Total users in system: <strong>" . $userCount . "</strong></p>\n";
            
            // Check users by role
            $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
            $userRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<p>Users by role:</p>\n<ul>\n";
            foreach ($userRoles as $role) {
                echo "<li><strong>" . htmlspecialchars($role['role']) . ":</strong> " . $role['count'] . "</li>\n";
            }
            echo "</ul>\n";
            
        } catch (Exception $e) {
            echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
            echo "<p>Please check your database connection settings in config/dbconnection.php</p>\n";
        }
        ?>
        
        <a href="auth/php/admin-dashboard.php">← Back to Admin Dashboard</a>
    </div>
</body>
</html>