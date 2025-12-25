<?php
// Script to add certificate_path column to results table
require_once 'config/dbconnection.php';

echo "<h1>Adding Certificate Path Column to Results Table</h1>";

try {
    // Check if the column already exists
    $checkColumn = $pdo->prepare("SHOW COLUMNS FROM results LIKE 'certificate_path'");
    $checkColumn->execute();
    $columnExists = $checkColumn->fetch();
    
    if ($columnExists) {
        echo "<p style='color: orange;'>Column 'certificate_path' already exists in the results table.</p>";
    } else {
        // Add the certificate_path column
        $addColumn = "ALTER TABLE results ADD COLUMN certificate_path VARCHAR(500) NULL AFTER percentage";
        $pdo->exec($addColumn);
        echo "<p style='color: green;'>Successfully added 'certificate_path' column to results table.</p>";
        
        // Add an index for better performance
        $addIndex = "CREATE INDEX idx_results_certificate_path ON results(certificate_path)";
        $pdo->exec($addIndex);
        echo "<p style='color: green;'>Successfully added index for 'certificate_path' column.</p>";
    }
    
    echo "<p style='color: blue;'>Database update completed successfully!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please check the database connection and permissions.</p>";
}

echo "<p><a href='index.php'>Back to Home</a></p>";
?>